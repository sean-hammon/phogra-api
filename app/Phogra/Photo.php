<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Query;
use Illuminate\Support\Facades\DB;

/**
 * Class Photo
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Photo
{
	private $query;

    private $photosTable = 'photos';
    private $filesTable = 'files';

	public function __construct(Query $query) {
		$this->query = $query;
	}

    /**
     * @return object
     */
    public function create($data)
    {
        try {
			if (!isset($data['slug']) || empty($data['slug'])) {
				$data['slug'] = str_slug($data['title']);
			}
			if (!isset($data['parent_id'])) {
				$data['parent_id'] = null;
			}
			if (!isset($data['node'])) {
				$data['node'] = $this->makeNode($data);
			}

			return PhotoModel::create($data);
        }
        catch(\Exception $e) {
            // TODO: Do something if the insert fails
        }
    }


	/**
	 * Fetch all gallery rows
	 *
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
    public function all($params) {
		$this->initQuery($params);

		return DB::select($this->query->sql(), $this->query->variables());
	}


	/**
	 * Fetch a single gallery result
	 *
	 * @param $id	   int     row id of the gallery
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
	public function one($id, $params) {
		$this->initQuery($params);

		$this->query->addWhere("AND `{$this->photosTable}`.`id` = :id", ["id" => $id]);
		$result = DB::select($this->query->sql(), $this->query->variables());

		if (count($result)) {
			return $result[0];
		} else {
			return null;
		}
	}


	/**
	 * Fetch multiple gallery rows based on a comma separated list
	 *
	 * @param $list	   string  comma separated row ids of the galleries
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
	public function multiple($list, $params) {

		$this->initQuery($params);
		$this->query->addWhere("AND `{$this->photosTable}`.`id` IN ({$list})");

		$result = DB::select($this->query->sql(), $this->query->variables());

		if (count($result)) {
			return $result;
		} else {
			return null;
		}
	}

	private function makeNode($data) {
		$max_node = PhotoModel::where('parent_id', $data['parent_id'])->max('node');

		if (is_null($max_node)) {
			$parent_node = PhotoModel::where('id', $data['parent_id'])->max('node');
			if (is_null($parent_node)) {
				return "001";
			}

			return $parent_node.":001";
		}

		$tree = explode(":", $max_node);
		$int = (int)array_pop($tree);
		$tree[] = sprintf('%03d',++$int);
		return implode(":", $tree);
	}

	/**
	 * Initialize the table query with SQL common to all queries
	 *
	 * @param $params  object  the parameter object created by the BaseController
	 *
	 * @return \Illuminate\Database\Query\Builder;
	 */
	private function initQuery($params) {

		//	Retrieve files
		$fileJoin = <<<EOT
			  LEFT JOIN (SELECT
						   `photo_id`,
						   GROUP_CONCAT(id SEPARATOR ',') AS file_ids
						 FROM `{$this->filesTable}`
						 GROUP BY `photo_id`)
				AS {$this->filesTable}
				ON `{$this->filesTable}`.`photo_id` = `{$this->photosTable}`.`id`
EOT;

		$this->query->reset();
		$this->query->setTable($this->photosTable);
		$this->query->setSelect([
				"`{$this->photosTable}`.*",
				"`{$this->filesTable}`.file_ids"
			]);
		$this->query->addJoin($fileJoin);
		$this->query->addWhere("`{$this->photosTable}`.`deleted_at` IS NULL");

		$this->applyParams($params);
	}

	/**
	 * Look through the parameter object and modify the query as necessary
	 *
	 * @param $params  object  the parameter object created by the BaseController
	 */
	private function applyParams($params) {
		$this->hasFields($params);
	}

	/**
	 * Add select fields if necessary
	 *
	 * @param $params  object   the parameter object created by the BaseController
	 */
	private function hasFields($params) {
		$table = $this->photosTable;
		if (isset($params->fields->$table)) {

			//  This will overwrite the select statement that adds the files
			//  and photo ids to the results. According to the spec at http://jsonapi.org
			//  if you are specifying fields and you want child objects you need to
			//  add those to your field list.
			$this->query->setSelect($params->fields->$table);
		}
	}

}