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

    public function __construct(array $data) {
        $this->rowData = $data;
        if (!isset($this->rowData['slug']) || empty($this->rowData['slug'])) {
            $this->rowData['slug'] = str_slug($this->rowData['title']);
        }
    }

    /**
     * @return object
     */
    public static function create($data)
    {
        try {
            //  Returning an Eloquent model here feels oogy to me.
            $row = GalleryModel::create($data);
            return $row->getAttributes();
        }
        catch(Exception $e) {
            // Do something if the insert fails
        }
    }

    public static function all($params) {
		$query = self::table();

		$table = self::$tableName;
		if (isset($params->fields->$table)) {
            $query->select($params->fields->$table);
		} else {
			$query->addSelect("galleries.*");
		}

		return $query->get();
	}

	/**
	 * Initialize the table query with SQL common to all queries
	 * @return \Illuminate\Database\Query\Builder;
	 */
	private static function table() {
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

		return $query;
	}
}