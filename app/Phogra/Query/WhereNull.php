<?php

namespace App\Phogra\Query;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

class WhereNull implements QueryModifier
{
	private $column;

	public function __construct($column) {
		$this->column = $column;
	}

	/**
	 * Apply the where clause to a query builder object
	 *
	 * @param Builder|JoinClause $query the query object to apply where
	 * @return void
	 */
	public function apply(&$query) {
			$query->whereNull($this->column);
	}
}