<?php

namespace App\Phogra;

use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\UnknownException;
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

    private $galleryTable   = 'galleries';
    private $photoJoinTable = 'gallery_photos';

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Add one or more new galleries.
     *
     * When sending multiple galleries with position data, order becomes important
     * because each row is processed independently. Depending on the the order and
     * the position values you supply, you might not get what you expect.
     *
     * @param array $data row data for insert
     *
     * @return \App\Phogra\Eloquent\Gallery
     * @throws BadRequestException
     * @throws UnknownException
     */
    public function create($data)
    {
        if (!isset($data['title'])) {
            $new_galleries = [];
            foreach ($data as $row) {
                $new_galleries[] = $this->addNew($row);
            }
            return $new_galleries;
        }

        return $this->addNew($data);
    }

    private function addNew($row)
    {
        if (!isset($row['title'])) {
            throw new BadRequestException("Title is a required field.");
        }
        try {
            if (!isset($row['slug']) || empty($row['slug'])) {
                $row['slug'] = str_slug($row['title']);
            }
            if (!isset($row['parent_id'])) {
                $row['parent_id'] = null;
            }
            if (!isset($row['node'])) {
                $this->makeNode($row);
            }
            if (isset($row['shared'])) {
            	unset($row['shared']);
	            $row['protected'] = 1;
	            $row['share_key'] = uniqid();
            }

            return GalleryModel::create($row);
        } catch (\Exception $e) {
            throw new UnknownException($e->getMessage());
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
    public function all($params)
    {
        $this->initQuery($params);

        return $this->query->get();
    }

    /**
     * Fetch a single gallery result. Force empty to be true.
     * Requests for specific ids should always return results if the ids exist.
     *
     * @param $id       int     row id of the gallery
     * @param $params  object  parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function one($id, $params)
    {
        $params->empty = "true";
        $this->initQuery($params);

        $this->query->where("{$this->galleryTable}.id", "=", $id);

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
     * @param $ids      integer[] a collection of row ids
     * @param $params object    parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function multiple($ids, $params)
    {
        $params->empty = "true";
        $this->initQuery($params);

        $this->query->whereIn("{$this->galleryTable}.id", $ids);

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
     * @param array $data row data for the gallery
     *
     * @return string
     */
    private function makeNode(&$data)
    {
        $max_node = GalleryModel::where('parent_id', $data['parent_id'])->max('node');

        if (is_null($max_node)) {
            $parent = GalleryModel::find($data['parent_id']);
            if (is_null($parent)) {
                $new_node = "001";
            } else {
                $new_node = $parent->node . ":001";
            }
        } else {
            $tree = explode(":", $max_node);
            if (isset($data['position'])) {
                $this->insertNodeAt($data);
                $new_node = $this->getNodeAt($tree, $data['position']);

                //  Get rid of the position attribute so Eloquent doesn't
                //  use it in the query.
                unset($data['position']);
            } else {
                $new_node = $this->incrementNode($tree);
            }
        }

        $data['node'] = $new_node;
    }

    /**
     * Create a node string for a specific position in the tree.
     *
     * @param array $tree
     * @param int $position
     *
     * @return string
     */
    private function getNodeAt($tree, $position)
    {
        array_pop($tree);
        $tree[] = sprintf('%03d', $position);
        return implode(":", $tree);
    }

    /**
     * Get the next node string in the tree.
     *
     * @param array $tree
     *
     * @return string
     */
    private function incrementNode($tree)
    {
        $int = (int)array_pop($tree);
        $tree[] = sprintf('%03d', ++$int);
        return implode(":", $tree);
    }

    private function insertNodeAt($data)
    {
        $parent = GalleryModel::find($data['parent_id']);
        $parent_node = explode(":", $parent->node);
        $targetIdx = count($parent_node);

        //  Order By node DESC keeps you from running into unique constraint problems
        $affected = GalleryModel::where('node', "like", $parent->node . ":%")->orderBy("node", "desc")->get();
        $pos = (int)$data['position'];

        foreach ($affected as $aNode) {
            $tree = explode(":", $aNode->node);
            $intVal = (int)$tree[$targetIdx];
            if ($intVal >= $pos) {
                $tree[$targetIdx] = sprintf('%03d', $intVal + 1);
                $aNode->node = implode(":", $tree);
                $aNode->save();
            }
        }
    }

    /**
     * Initialize the table query with SQL common to all queries
     *
     * @param $params  object  the parameter object created by the BaseController
     *
     * @return \Illuminate\Database\Query\Builder;
     */
    private function initQuery($params)
    {

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
        $this->query->where("{$this->galleryTable}.protected", "=", 0);
        $this->query->orderBy("{$this->galleryTable}.node");

        if (isset($this->user)) {
            $this->query->orWhere(function ($query) {
                $query->where("{$this->galleryTable}.protected", "=", 1);
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
            $this->query->where(function ($query) {
                $query->whereNotNull("photos")
                      ->orWhere("total_count", ">", 0);
            });
        }

        $this->applyParams($params);
    }

    /**
     * Look through the parameter object and modify the query as necessary
     *
     * @param $params  object  the parameter object created by the BaseController
     */
    private function applyParams($params)
    {
        $this->hasFields($params);
    }

    /**
     * Add select fields if necessary
     *
     * @param $params  object   the parameter object created by the BaseController
     */
    private function hasFields($params)
    {
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