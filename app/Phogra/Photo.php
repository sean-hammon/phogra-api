<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Exception\InvalidParameterException;
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
	 * @param $data
	 *
	 * @return object
	 * @throws InvalidParameterException
	 */
    public function create($data)
    {
		$gallery_id = null;
		$exception = '';
		if (isset($data['gallery_id'])) {
			$gallery_id = $data[$gallery_id];
			unset($data[$gallery_id]);
		}
		if (!isset($data['title']) && !isset($data['slug'])) {
			$exception .= "You must specify at least a title or a slug.";
		}
		if ($exception) {
			throw new InvalidParameterException($exception);
		}

		$toCheck = isset($data['slug']) ? $data['slug'] : str_slug($data['title']);
		$slugCheck = PhotoModel::where('slug', '=', $toCheck)->first();
		if ($slugCheck != null) {
			throw new InvalidParameterException('The slug "' . $toCheck . '" already exists in the database. " .
				"Slugs must be unique. If you didn\'t specify a slug, it was generated from the title. " .
				"Either change the title or specify a slug that is unique.');
		}

		if (!isset($data['slug']) || empty($data['slug'])) {
			$data['slug'] = str_slug($data['title']);
		}
		if (!isset($data['parent_id'])) {
			$data['parent_id'] = null;
		}
		if (!isset($data['node'])) {
			$data['node'] = $this->makeNode($data);
		}

		$photo = PhotoModel::create($data);

		//TODO: Handle gallery data

		return $photo;
    }


	/**
	 * Fetch a single photo result
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
	 * Fetch multiple photo rows based on a comma separated list
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