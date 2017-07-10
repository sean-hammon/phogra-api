<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Tag as TagModel;
use App\Phogra\Query\Join;
use App\Phogra\Query\JoinParams;
use App\Phogra\Query\Table;
use Illuminate\Support\Facades\DB;

class Tag
{
	private $query;

	private $tagTable;
	private $photoJoinTable;


	public function __construct()
	{
		$this->tagTable = Table::tags;
		$this->photoJoinTable = Table::photo_tags;
	}


    /**
     * Fetch all gallery rows
     *
     * @param $params  object  parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function all($params)
    {
        $this->initQuery($params);

        return $this->query->get();
    }


    public function one($name, $params)
    {
        $this->initQuery($params);

        $this->query->where(Table::tags . ".name", "=", $name);
        
        return $this->query->get();
    }


	public function create($name)
	{
		$tag = TagModel::where('name', '=', $name)->first();

		if (empty($tag)) {

			$tag = TagModel::create(['name' => $name]);

		} else {

			$warnings = app('Warnings');
			$warnings->addWarning('Tag "' . $name . '" already exists.');

		}

		return $tag;
	}



	/**
	 * Initialize the table query with SQL common to all queries
	 *
	 * @param $params  object  the parameter object created by the BaseController
	 *
	 * @return void
	 */
	private function initQuery($params)
	{

		$this->query = DB::table($this->tagTable);

		//	Get IDs of any photos in a gallery for relationship related links
		$photoParams = new JoinParams();
		$photoParams->as = 'photos';
		$photoParams->raw = "
			(SELECT
				`tag_id`,
				GROUP_CONCAT(photo_id SEPARATOR ',') AS photos
			FROM `{$this->photoJoinTable}`
			GROUP BY `tag_id`)
			AS {$photoParams->as}";
		$photoParams->on = ["{$photoParams->as}.tag_id", "=", "{$this->tagTable}.id"];
		$photoJoin = new Join($photoParams);
		$photoJoin->apply($this->query);

		$this->query->select(
			"{$photoParams->as}.photos",
			"{$this->tagTable}.*"
		);

		$this->applyParams($params);
	}


    /**
     * Look through the parameter object and modify the query as necessary
     *
     * @param $params  object  the parameter object created by the BaseController
     */
    private function applyParams($params)
    {
        //  Tags doesn't need parameters...yet?
    }
}