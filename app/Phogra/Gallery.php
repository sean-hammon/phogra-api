<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Gallery as GalleryModel;
use Illuminate\Support\Facades\DB;

/**
 * Class Gallery
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Gallery
{
    public static $tableName = 'galleries';
    public static $photoJoin = 'gallery_photos';

    /**
     * @return object
     */
    public static function create($data)
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
    public static function all($params) {
		$query = self::table($params);

		return $query->get();
	}


	/**
	 * Fetch a single gallery result
	 *
	 * @param $id	   int     row id of the gallery
	 * @param $params  object  parameter object created in BaseController
	 *
	 * @return array|static[]
	 */
	public static function one($id, $params) {
		$query = self::table($params);

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
	public static function multiple($list, $params) {
		$query = self::table($params);

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
	private static function table($params) {
		$children = DB::table(self::$tableName)
			->select('parent_id', DB::raw("GROUP_CONCAT(id SEPARATOR ',') as children"))
			->groupBy('parent_id');
		$photos = DB::table(self::$photoJoin)
			->select('gallery_id', DB::raw("GROUP_CONCAT(photo_id SEPARATOR ',') as photos"))
			->groupBy('gallery_id');


		$query = DB::table(self::$tableName)
			->select("children.children", "photos.photos")
			->leftJoin(DB::raw("({$children->toSql()}) as children"), "children.parent_id", "=", self::$tableName.".id" )
			->mergeBindings($children)
			->leftJoin(DB::raw("({$photos->toSql()}) as photos"), "photos.gallery_id", "=", self::$tableName.".id")
			->mergeBindings($photos)
			->whereNull('deleted_at');

		self::applyParams($query, $params);

		return $query;
	}

	/**
	 * Look through the parameter object and modify the query as necessary
	 *
	 * @param $query   \Illuminate\Database\Query\Builder  the query builder class
	 * @param $params  object                              the parameter object created by the BaseController
	 */
	private static function applyParams(&$query, $params) {
		self::hasFields($query, $params);
	}

	/**
	 * Add select fields if necessary
	 *
	 * @param $query   \Illuminate\Database\Query\Builder  the query builder class
	 * @param $params  object                              the parameter object created by the BaseController
	 */
	private static function hasFields(&$query, $params) {
		$table = self::$tableName;
		if (isset($params->fields->$table)) {

			//  This will overwrite the select statement that adds the children
			//  and photo ids to the results. According to the spec at http://jsonapi.org
			//  if you are specifying fields and you want child objects you need to
			//  add those to your field list.
			$query->select($params->fields->$table);
		} else {
			$query->addSelect("galleries.*");
		}

	}

}