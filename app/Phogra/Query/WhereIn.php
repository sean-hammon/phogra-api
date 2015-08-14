<?php

namespace App\Phogra\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

class WhereIn implements QueryModifier
{
	private $column;
	private $values;

	public function __construct($column, $values) {
		$this->column = $column;
		if (is_array($values)) {
			$this->values = $values;
		} else {
			$this->values[] = $values;
		}
	}

	public function addValue($value){
		$this->values[] = $value;
	}

	public function addValues(array $values) {
		$this->values = array_merge($this->values, $values);
	}

	/**
	 * Apply the where clause to a query builder object
	 *
	 * @param Builder|JoinClause $query the query object to apply where
	 * @return void
	 */
	public function apply(&$query) {
		if (count($this->values) == 1) {
			$query->where($this->column, '=', $this->values[0]);

		} else {
			$query->whereIn($this->column, $this->values);
		}
	}
}