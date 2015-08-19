<?php

namespace App\Phogra\Query;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Class Join
 *
 * Joins can get messy and the Laravel Builder syntax doesn't seem to allow for assembling
 * complex joins as you go. This is not intended to be a comprehensive solution. It's just
 * enough to get done what I need.
 *
 * @package App\Phogra\Query
 */
class Join implements QueryModifier
{
	private $raw;
	private $table;
	private $on;
	private $where;
	private $type;

	public function __construct(JoinParams $params) {
		$this->raw = isset($params->raw) ? preg_replace('/[\t\n]+/', ' ', $params->raw) : null;
		$this->table = isset($params->table) ? $params->table : null;
		$this->on = isset($params->on) ? $params->on : null;
		$this->type = isset($params->type) ? $params->type : null;
	}

	public function addWhere(QueryModifier $where) {
		$this->where[] = $where;
	}

	/**
	 * Apply the where clause to a query builder object
	 *
	 * @param Builder $query the query object to apply where
	 *
	 * @return void
	 */
	public function apply(&$query)
	{
		$theTable = $this->table;
		if (isset($this->raw)) {
			$theTable = DB::raw($this->raw);
		}
		switch ($this->type) {
			case 'left':
				$query->leftJoin($theTable, function($join) {
					$this->clauseFn($join);
				});
				break;

			default:
				$query->join($theTable, function($join) {
					$this->clauseFn($join);
				});
		}
	}

	private function clauseFn(JoinClause &$join) {
		$join->on($this->on[0], $this->on[1], $this->on[2]);
		if (isset($this->where)) {
			foreach ($this->where as $where) {
				$where->apply($join);
			}
		}
	}
}