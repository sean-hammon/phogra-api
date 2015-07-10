<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Gallery as GalleryModel;
use App\Phogra\Query;
use Illuminate\Support\Facades\DB;

/**
 * Class Gallery
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Gallery
{
	private $query;

    private $tableName = 'galleries';
    private $photoJoin = 'gallery_photos';

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
            $row = GalleryModel::create($data);
			//  Returning an Eloquent model here feels oogy to me.
            return (object)$row->getAttributes();
        }
        catch(\Exception $e) {
            // Do something if the insert fails
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

		return DB::select($this->query);
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
		$query = $this->initQuery($params);

		$query->where("id", "=", $id);

		return $query->first();
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
		$query = $this->initQuery($params);

		$anArray = explode(',', $list);
		$query->whereIn("id", $anArray);

		return $query->get();
	}


	/**
	 * Initialize the table query with SQL common to all queries
	 *
	 * @param $params  object  the parameter object created by the BaseController
	 *
	 * @return \Illuminate\Database\Query\Builder;
	 */
	private function initQuery($params) {

		//	Retrieve sub galleries
		$childJoin = <<<EOT
			  LEFT JOIN (SELECT
						   `parent_id`,
						   GROUP_CONCAT(id SEPARATOR ',') AS children
						 FROM `{$this->tableName}`
						 GROUP BY `parent_id`)
				AS children
				ON `children`.`parent_id` = `galleries`.`id`
EOT;
		//	Get IDs of any photos in a gallery for relationship related links
		$photosJoin = <<<EOT
			  LEFT JOIN (SELECT
						   `gallery_id`,
						   GROUP_CONCAT(photo_id SEPARATOR ',') AS photos
						 FROM `{$this->photoJoin}`
						 GROUP BY `gallery_id`)
				AS photos
				ON `photos`.`gallery_id` = `galleries`.`id`
EOT;
		//	Check for photos contained by a parent gallery that doesn't have
		//	photos of its own.
		$photoCountJoin = <<<EOT
			  JOIN (SELECT
					  id,
					  (SELECT count(photo_id)
					   FROM `{$this->photoJoin}`
					   WHERE `gallery_id` IN (SELECT `id`
											FROM `{$this->tableName}` AS g
											WHERE `node` LIKE CONCAT(galleries.node,
																   ':%'))) AS total_count
					FROM `{$this->tableName}`) AS photo_counts
				ON `photo_counts`.`id` = `galleries`.`id`
EOT;

		$this->query->reset();
		$this->query->setTable($this->tableName);
		$this->query->setSelect([
				'`children`.`children`',
				'`photos`.`photos`',
				'`galleries`.*'
			]);
		$this->query->addJoin($childJoin);
		$this->query->addJoin($photosJoin);
		$this->query->addWhere("`{$this->tableName}`.`deleted_at` IS NULL");

		//	By default only return galleries that have photos, unless empty = true
		//	then return them all.

		if (!isset($params->empty) || $params->empty == 'false') {
			$this->query->addSelectColumns('`photo_counts`.`total_count`');
			$this->query->addJoin($photoCountJoin);
			$this->query->addWhere('AND (photos IS NOT NULL OR total_count > 0)');
		}

		$this->applyParams($params);
/*
		SELECT
  `children`.`children`,
  `photos`.`photos`,
  photo_counts.total_count,
  `galleries`.*
FROM `galleries`
  JOIN (SELECT
          id,
          (SELECT count(photo_id)
           FROM gallery_photos
           WHERE gallery_id IN (SELECT id
                                FROM galleries AS g
                                WHERE node LIKE CONCAT(galleries.node,
													   ':%'))) AS total_count
        FROM galleries) AS photo_counts
    ON photo_counts.id = galleries.id
  LEFT JOIN (SELECT
               `parent_id`,
               GROUP_CONCAT(id SEPARATOR ',') AS children
             FROM `galleries`
             GROUP BY `parent_id`)
    AS children
    ON `children`.`parent_id` = `galleries`.`id`
  LEFT JOIN (SELECT
               `gallery_id`,
               GROUP_CONCAT(photo_id SEPARATOR ',') AS photos
             FROM `gallery_photos`
             GROUP BY `gallery_id`)
    AS photos
    ON `photos`.`gallery_id` = `galleries`.`id`
WHERE `deleted_at` IS NULL
		*/

/*
		$children = DB::table(self::$tableName)
			->select('parent_id', DB::raw("GROUP_CONCAT(id SEPARATOR ',') as children"))
			->groupBy('parent_id');
		$photos = DB::table(self::$photoJoin)
			->select('gallery_id', DB::raw("GROUP_CONCAT(photo_id SEPARATOR ',') as photos"))
			->groupBy('gallery_id');
		$counts = DB::table(self::$tableName)
			->select('id', DB::raw())
			select id, (SELECT count(photo_id)

		$query = DB::table(self::$tableName)
			->select("children.children", "photos.photos")
			->leftJoin(DB::raw("({$children->toSql()}) as children"), "children.parent_id", "=", self::$tableName.".id" )
			->mergeBindings($children);
*/
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
		$table = $this->tableName;
		if (isset($params->fields->$table)) {

			//  This will overwrite the select statement that adds the children
			//  and photo ids to the results. According to the spec at http://jsonapi.org
			//  if you are specifying fields and you want child objects you need to
			//  add those to your field list.
			$this->query->setSelect($params->fields->$table);
		}
	}

}