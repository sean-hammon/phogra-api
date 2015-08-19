<?php

namespace App\Phogra;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Query\Join;
use App\Phogra\Query\JoinParams;
use App\Phogra\Query\WhereIn;


/**
 * Class Photo
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Photo
{
	/**
	 * @var Builder
	 */
	private $query;

    private $photosTable = 'photos';
    private $filesTable = 'files';

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

		$this->query->where("{$this->photosTable}.id", "=", $id);
		$result = $this->query->get();

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
		$ids = explode(",", $list);
		$this->query->whereIn("{$this->photosTable}.id", $ids);

		$result = $this->query->get();

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

		$this->query = DB::table($this->photosTable);


		$joinParams = new JoinParams();
		$joinParams->as = 'relationships';
		$joinParams->raw = "(SELECT
						   photo_id,
						   GROUP_CONCAT({$this->filesTable}.id SEPARATOR ',') AS file_ids,
						   GROUP_CONCAT({$this->filesTable}.type SEPARATOR ',') AS file_types
						 FROM {$this->filesTable}
						 GROUP BY photo_id)
						 AS {$joinParams->as}";
		$joinParams->on = ["{$joinParams->as}.photo_id", "=", "{$this->photosTable}.id"];
		$join = new Join($joinParams);
		$join->apply($this->query);


		$this->query->select("{$this->photosTable}.*", "{$joinParams->as}.file_types");
		$this->query->whereNull("{$this->photosTable}.deleted_at");

		$this->applyParams($params);
	}

	/**
	 * Look through the parameter object and modify the query as necessary
	 *
	 * @param $params  object  the parameter object created by the BaseController
	 */
	private function applyParams($params) {
		$this->hasFields($params);
		$this->hasIncludes($params);
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
			$this->query->select($params->fields->$table);
		}
	}

	/**
	 * Add hydrated related objects
	 *
	 * @param $params  object   the parameter object created by the BaseController
	 * @throws BadRequestException
	 */
	private function hasIncludes($params) {
		if (empty($params->include)) {
			return;
		}

		$filesJoin = null;
		$filesWhere = null;

		foreach ($params->include as $relation) {
			if (is_array($relation)) {
				switch($relation[0]) {
					case "files":
						if (count($relation) > 2) {
							throw new BadRequestException(implode(".",$relation) . " is not a valid relationship.");
						}
						$validTypes = array_keys(get_object_vars(config("phogra.fileTypes")));
						if( !in_array($relation[1], $validTypes)) {
							throw new BadRequestException(implode(".",$relation) . " is not a valid relationship.");
						}

						if (is_null($filesJoin)) {
							$filesJoin = $this->joinFiles();
						}
						if (is_null($filesWhere)) {
							$filesWhere = new WhereIn('files.type', $relation[1]);
							$filesJoin->addWhere($filesWhere);
						} else {
							$filesWhere->addValue($relation[1]);
						}
						break;

					case "users":
						//	TODO: Someday?
						throw new BadRequestException(implode(".",$relation) . " is not a valid relationship.");
						break;

					default:
						throw new BadRequestException(implode(".",$relation) . " is not a valid relationship.");

				}

				continue;
			}

			switch($relation) {
				case 'files':
					$filesJoin = $this->joinFiles();
					break;

				case "users":
					//	TODO: Someday?
					throw new BadRequestException("$relation is not a valid relationship.");
					break;

				default:
					throw new BadRequestException("$relation is not a valid relationship.");
			}
		}

		if (isset($filesJoin)) {
			$filesJoin->apply($this->query);
		}
	}

	/**
	 * Build the Join object to do a join to the files table.
	 *
	 * @return Join
	 */
	private function joinFiles() {
		$this->query->addSelect([
			"files.id as file_id", "type", "mimetype",
			"height", "width", "bytes", "hash",
			"files.created_at as file_created_at",
			"files.updated_at as file_updated_at"]
		);
		$this->query->orderBy("{$this->photosTable}.id");
		$this->query->orderBy("{$this->filesTable}.type");

		$joinParams = new JoinParams();
		$joinParams->table = $this->filesTable;
		$joinParams->on = [
			"{$this->filesTable}.photo_id",
			"=",
			"{$this->photosTable}.id"
		];
		return new Join($joinParams);
	}
}