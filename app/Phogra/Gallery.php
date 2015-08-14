<?php

namespace App\Phogra;

use Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Phogra\Query\Join;
use App\Phogra\Query\JoinParams;
use App\Phogra\Eloquent\Gallery as GalleryModel;

/**
 * Class Gallery
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Gallery
{
	/**
	 * @var Builder
	 */
	private $query;

    private $galleryTable = 'galleries';
    private $photoJoinTable = 'gallery_photos';


	public function __construct() {
		$this->user = Auth::user();
	}

	/**
	 * @param array $data row data for insert
	 *
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

			return GalleryModel::create($data);
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

		return $this->query->get();
	}


	/**
	 * Fetch a single gallery result. Force empty to be true.
	 * Requests for specific ids should always return results if the ids exist.
	 *
	 * @param $id	   int     row id of the gallery
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
	public function one($id, $params) {
		$params->empty = "true";
		$this->initQuery($params);

		$this->query->where("{$this->tableName}.id", "=", $id);

		$result = $this->query->get();

		if (count($result)) {
			return $result[0];
		} else {
			return null;
		}
	}


	/**
	 * Fetch multiple gallery rows based on a comma separated list. Force empty to be true.
	 * Requests for specific ids should always return results if the ids exist.
	 *
	 * @param $list	   string  comma separated row ids of the galleries
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
	public function multiple($list, $params) {
		$params->empty = "true";
		$this->initQuery($params);

		$ids = explode(",", $list);
		$this->query->whereIn("{$this->tableName}.id", $ids);

		$result = $this->query->get();

		if (count($result)) {
			return $result;
		} else {
			return null;
		}
	}

	/**
	 * Generate the tree node data that is used in SQL queries.
	 *
	 * @param $data  array  row data for the gallery
	 *
	 * @return string
	 */
	private function makeNode($data) {
		$max_node = GalleryModel::where('parent_id', $data['parent_id'])->max('node');

		if (is_null($max_node)) {
			$parent_node = GalleryModel::where('id', $data['parent_id'])->max('node');
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

		$this->query = DB::table($this->galleryTable);

		//	Retrieve sub galleries
		$childParams = new JoinParams();
		$childParams->as = 'children';
		$childParams->raw = "(SELECT
						   `parent_id`,
						   GROUP_CONCAT(id SEPARATOR ',') AS children
						 FROM `{$this->galleryTable}`
						 GROUP BY `parent_id`)
						 AS {$childParams->as}";
		$childParams->on = ["{$childParams->as}.parent_id", "=", "{$this->galleryTable}.id"];
		$childParams->type = 'left';
		$childJoin = new Join($childParams);
		$childJoin->apply($this->query);

		//	Get IDs of any photos in a gallery for relationship related links
		$photoParams = new JoinParams();
		$photoParams->as = 'photos';
		$photoParams->raw = "
			(SELECT
				`gallery_id`,
				GROUP_CONCAT(photo_id SEPARATOR ',') AS photos
			FROM `{$this->photoJoinTable}`
			GROUP BY `gallery_id`)
			AS {$photoParams->as}";
		$photoParams->on = ["{$photoParams->as}.gallery_id", "=", "{$this->galleryTable}.id"];
		$photoParams->type = "left";
		$photoJoin = new Join($photoParams);
		$photoJoin->apply($this->query);

		$this->query->select(
			"{$childParams->as}.children",
			"{$photoParams->as}.photos",
			"{$this->galleryTable}.*"
		);
		$this->query->whereNull("{$this->galleryTable}.deleted_at");
		$this->query->where("{$this->galleryTable}.protected", "=",  0);

		if (isset($this->user)) {
			$this->query->orWhere(function($query){
				$query->where("{$this->galleryTable}.protected", "=" ,1);
				$query->whereRaw(
					"{$this->galleryTable}.id IN
					(SELECT gallery_id
						FROM {$this->userJoinTable}
						WHERE user_id = {$this->user->id}
					)"
				);
			});
		}

		//	By default only return galleries that have photos, unless empty = true
		//	then return them all.
		$countParams = new JoinParams();
		$countParams->as = 'photo_counts';
		$countParams->raw =
			"(SELECT
				id,
				(SELECT count(photo_id)
					FROM {$this->photoJoinTable}
					WHERE gallery_id IN
						(SELECT `id`
							FROM {$this->galleryTable} AS g
							WHERE node LIKE CONCAT({$this->galleryTable}.node, ':%'))
						) AS total_count
					FROM {$this->galleryTable}
				) AS {$countParams->as}";
		$countParams->on = ["{$countParams->as}.id", "=", "{$this->galleryTable}.id"];
		$countJoin = new Join($countParams);

		if (!isset($params->empty) || $params->empty == 'false') {
			//	Check for photos contained by a parent gallery that doesn't have
			//	photos of its own.
			$countJoin->apply($this->query);

			$this->query->addSelect("{$countParams->as}.total_count");
			$this->query->where(function($query){
				$query->whereNotNull("photos")
					->orWhere("total_count", ">",  0);
			});
		}

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
		$table = $this->galleryTable;
		if (isset($params->fields->$table)) {

			//  This will overwrite the select statement that adds the children
			//  and photo ids to the results. According to the spec at http://jsonapi.org
			//  if you are specifying fields and you want child objects you need to
			//  add those to your field list.
			$this->query->setSelect($params->fields->$table);
		}
	}

}