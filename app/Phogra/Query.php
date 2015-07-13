<?php

namespace app\Phogra;

class Query
{
	private $table = '';
	private $selects = [];
	private $joins = [];
	private $wheres = [];
	private $order = [];
	private $group = [];
	private $limit = [];
	private $vars = [];

	public function reset() {
		$this->table = '';
		$this->selects = [];
		$this->joins = [];
		$this->wheres = [];
		$this->order = [];
		$this->group = [];
		$this->limit = [];
		$this->vars = [];
	}

	public function setTable($table) {
		$this->table = $table;
	}

	public function setSelect($columns){
		if (is_array($columns)) {
			$this->selects = $columns;
		} else {
			$this->selects = array($columns);
		}
	}

	public function addSelectColumns($columns) {
		$this->selects[] = $columns;
	}

	public function addJoin($join) {
		$this->joins[] = $join;
	}

	public function addWhere($where, $vars = null) {
		$this->wheres[] = $where;
		if ($vars != null) {
			$this->vars = array_merge($this->vars, $vars);
		}
	}

	public function variables() {
		return $this->vars;
	}

	public function sql() {
		return $this->__toString();
	}

	public function toString() {
		return $this->__toString();
	}

	public function __toString() {
		$query = "SELECT " .
			implode(', ', $this->selects) . "\n" .
			"FROM " . $this->table . "\n" .
			implode("\n", $this->joins) . "\n" .
			"WHERE " . implode(' ', $this->wheres);

		return str_replace("\t", '', $query);
	}
}