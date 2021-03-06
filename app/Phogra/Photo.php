<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Gallery as GalleryModel;
use App\Phogra\Query\Table;
use App\Phogra\Eloquent\Photo as PhotoModel;
use App\Phogra\Exception\BadRequestException;
use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Query\Join;
use App\Phogra\Query\JoinParams;
use App\Phogra\Query\WhereIn;
use App\Phogra\Query\WhereNull;
use App\Phogra\Response\ExceptionResponse;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Hashids;

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

    /**
     * @param $data
     * @return object
     * @throws BadRequestException
     * @throws InvalidParameterException
     */
    public function create($data)
    {
        $gallery_ids = [];
        $exception = '';
        if (isset($data['gallery_ids'])) {
	        $gallery_ids = Helpers::extractIds($data['gallery_ids']);

            if (empty($gallery_ids)) {
	            $arrStr = str_replace("\n", "", print_r($data['gallery_ids'], true));
                throw new BadRequestException($arrStr . " is not a valid gallery hash");
            }

            unset($data['gallery_ids']);
            $data['canonical_gallery_id'] = $gallery_ids[0];
        }
        if (!isset($data['title']) && !isset($data['slug'])) {
            $exception .= "You must specify at least a title or a slug.";
        }
        if ($exception) {
            throw new InvalidParameterException($exception);
        }

        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = str_slug($data['title']);
        }

        $modified_slug = null;
        $slugCheck = PhotoModel::where('slug', '=', $data['slug'])->first();

        // ToDo: Add a request parameter so this if statement actually means something.
        if ($slugCheck != null && false) {
            throw new InvalidParameterException(
                'The slug "' . $data['slug'] . '" already exists in the database. '
            );
        } else {
            while ($slugCheck != null) {
                $modified_slug = $data['slug'] . '-' . bin2hex(openssl_random_pseudo_bytes(2));
                $slugCheck = PhotoModel::where('slug', '=', $modified_slug)->first();
            }
        }

        if ($modified_slug !== null) {
            $warnings = app('Warnings');
            $warnings->addWarning('Slug made unique: ' . $data['slug'] . ' changed to ' . $modified_slug);
            $data['slug'] = $modified_slug;
        }

        $photo = PhotoModel::create($data);
        if (isset($gallery_ids) && !empty($gallery_ids)) {
            foreach ($gallery_ids as $gid) {
				$gallery = GalleryModel::find($gid);
	            if ($gallery == null) {
		            $warnings->addWarning('Gallery not found: ' . Hashids::encode($gid));
	            } else {
		            $gallery->photos()->attach($photo);
	            }
            }
        }

	    if (isset($data['tags'])) {
		    $hash = Hashids::encode($photo->id);
			$this->tagPhotos([$hash], $data['tags']);
	    }

        return $photo;
    }

    /**
     * Fetch a single photo result
     *
     * @param $id       int     row id of the gallery
     * @param $params  object  parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function one($id, $params)
    {
        $this->initQuery($params);

        $this->query->where(Table::photos . ".id", "=", $id);
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
     * @param $ids      integer[] a collection of row ids
     * @param $params object    parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function multiple($ids, $params)
    {

        $this->initQuery($params);
        $this->query->whereIn(Table::photos . ".id", $ids);

        $result = $this->query->get();

        if (count($result)) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Fetch multiple photo rows based on a given gallery id. Returns photos of child galleries
     * if no photos exist in the specified gallery.
     *
     * @param $gallery_id  int     the gallery id to filter by
     * @param $params      object  parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function byGalleryId($gallery_id, $params)
    {

        $this->initQuery($params);
        $this->query->join(Table::gallery_photos, function ($join) use ($gallery_id) {
            $join->on(Table::gallery_photos . ".photo_id", "=", Table::photos . ".id")
                 ->where("gallery_id", "=", $gallery_id);
        });
        $result = $this->query->get();

        //  This gallery has photos. Send 'em back.
        if (count($result)) {
            return $result;
        }

        //  Gallery is empty, so we're going to assume it's a parent container and look for
        //  photos in it's children.
        //  TODO: Make this an option that can be triggered with a parameter
        $nodeQuery = DB::table(Table::galleries)
                       ->select('node')
                       ->where('id', '=', $gallery_id);
        $gallery = $nodeQuery->first();

        $this->initQuery($params);
        $this->query->join(Table::gallery_photos, Table::gallery_photos . ".photo_id", "=", Table::photos . ".id");
        $this->query->join(Table::galleries, function ($join) use ($gallery) {
            $join->on(Table::galleries . ".id", "=", Table::gallery_photos . ".gallery_id")
                 ->where("node", "like", $gallery->node . ":%");
        });

        $result = $this->query->get();

        if (count($result)) {
            return $result;
        }

        //  Total fail.
        return null;
    }

    /**
     * Fetch multiple photo rows based on a gallery id and photo ids. Not sure what use
     * this really is. If you have photo ids, the gallery_id is pointless, but for the sake
     * of hobgoblins in the API, here it is. https://en.wikiquote.org/wiki/Consistency
     *
     * I'm pretty sure the API doesn't return this as a link. As soon as I'm positive, I'll
     * take it out.
     *
     * @param $gallery_id  integer    the gallery id to filter by
     * @param $photo_ids   integer[]  a collection of row ids
     * @param $params      object     parameter object created in BaseController
     *
     * @return array|static[]
     */
    public function byGalleryAndPhotoIds($gallery_id, $photo_ids, $params)
    {

        $this->initQuery($params);
        $this->query->join(Table::gallery_photos, function ($join) use ($gallery_id, $photo_ids) {
            $join->on(Table::gallery_photos . ".photo_id", "=", Table::photos . ".id")
                 ->where("gallery_id", "=", $gallery_id)
                 ->whereIn(Table::gallery_photos . ".photo_id", $photo_ids);
        });

        $result = $this->query->get();

        if (count($result)) {
            return $result;
        } else {
            return null;
        }
    }

	/**
	 * Fetch multiple photos by tag.
	 *
	 * @param $tag      string  the tag to search on
	 * @param $params   object  parameter object created in BaseController
	 *
	 * @return array|null|static[]
	 */
    public function findByTag($tag, $params) {
		$this->initQuery($params);
	    $this->query->join(Table::photo_tags, Table::photo_tags . ".photo_id", "=", Table::photos . ".id");
	    $this->query->join(Table::tags, function($join) use ($tag) {
		    $join->on(Table::tags . ".id", "=", Table::photo_tags . ".tag_id")
			    ->where(Table::tags . ".name", "=", $tag);
	    });

	    $result = $this->query->get();

	    if (count($result)) {
		    return $result;
	    } else {
		    return null;
	    }
    }


    public function tagPhotos($photo_ids, $tags)
    {
	    $query = DB::table(Table::tags);
	    $query->whereIn('name', $tags);
	    $existing_tags = $query->get();

	    if (count($existing_tags) != count($tags)) {
		    //  We have incoming tags that aren't in the database.
		    $found_tags = [];
		    if (count($existing_tags) > 0){
			    //  Strip the tag value out of the row hash.
			    for( $i = 0; $i < count($existing_tags); $i++) {
				    $found_tags[] = $existing_tags[$i]->name;
			    }
			    //  These are the new tags that need to be added to the database.
			    $new_tags = array_diff($tags, $found_tags);
		    } else {
			    //  There were no existing tags. They need to all be added to the database.
			    $new_tags = $tags;
		    }

		    if (count($new_tags) > 0) {
			    //  Create an array of hashes for the insert statement.
			    $insert = array_map(function($item){
				    return ['name' => $item];
			    }, $new_tags);
			    DB::table(Table::tags)->insert($insert);
		    }

		    //  Now fetch the tag rows again with all the tags included.
		    $query = DB::table(Table::tags);
		    $query->whereIn('name', $tags);
		    $existing_tags = $query->get();
	    }

	    for ($i = 0; $i < count($photo_ids); $i++) {
			$photo_id = Hashids::decode($photo_ids[$i]);
		    for ($j = 0; $j < count($existing_tags); $j++) {
				$data = [
					"photo_id" => $photo_id[0],
					"tag_id" => $existing_tags[$j]->id
				];
			    //  try/catch is expensive, but I don't know if it's any more expensive than
			    //  querying each row before attempting an insert.
			    try{
				    DB::table(Table::photo_tags)->insert($data);
			    }
			    catch(\PDOException $e) {
				    //  23000 is a unique key violation. If it's any error other than that
				    //  re-throw the error.
				    if ($e->getCode() !== "23000") {
					    throw $e;
				    }
			    }
		    }
	    }

	    return true;

    }


    public function getFile($photo_ids, $file_types, $params)
    {
        $this->initQuery($params);
        $this->query->addSelect([
                                    "files.id as file_id", "type", "mimetype",
                                    "height", "width", "bytes", "hash",
                                    "files.created_at as file_created_at",
                                    "files.updated_at as file_updated_at"]
        );
        $this->query->where(Table::photos . ".id", "=", $photo_ids);
        $this->query->join(Table::files, function ($join) use ($file_types) {
            $join->on(Table::files . ".photo_id", "=", Table::photos . ".id")
                 ->where(Table::files . ".type", "=", $file_types);

            //TODO: need a way to turn this off
            if (true) {
                $join->whereNull(Table::files . ".deleted_at");
            }
        });

        $result = $this->query->first();
        if (!empty($result)) {
            return $result;
        }

        return null;
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

        $this->query = DB::table(Table::photos);

        $joinParams = new JoinParams();
        $joinParams->as = 'relationships';
        $joinParams->raw = "(SELECT
						   photo_id,
						   GROUP_CONCAT(" . Table::files . ".id SEPARATOR ',') AS file_ids,
						   GROUP_CONCAT(" . Table::files . ".type SEPARATOR ',') AS file_types
						 FROM " . Table::files . "
						 WHERE " . Table::files . ".deleted_at IS NULL 
						 GROUP BY photo_id)
						 AS {$joinParams->as}";
        $joinParams->on = ["{$joinParams->as}.photo_id", "=", Table::photos . ".id"];
        $join = new Join($joinParams);
        $join->apply($this->query);

	    $tagParams = new JoinParams();
	    $tagParams->as = 'tt';
	    $tagParams->type = "left";
	    $tagParams->raw = "(SELECT photo_id, 
	                            GROUP_CONCAT(t.name SEPARATOR ',') AS tags
	                        FROM " . Table::tags . " t
	                        JOIN " . Table::photo_tags ." pt ON pt.tag_id = t.id
	                            GROUP BY pt.photo_id)
	                        AS {$tagParams->as}";
	    $tagParams->on = ["{$tagParams->as}.photo_id", "=", Table::photos . ".id"];
	    $join = new Join($tagParams);
	    $join->apply($this->query);

        $this->query->select(Table::photos . ".*", "{$joinParams->as}.file_types", "{$tagParams->as}.tags");
        $this->query->whereNull(Table::photos . ".deleted_at");

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
        $this->hasIncludes($params);
    }

    /**
     * Add select fields if necessary
     *
     * @param $params  object   the parameter object created by the BaseController
     */
    private function hasFields($params)
    {
        $table = Table::photos;
        if (isset($params->fields->$table)) {

            //  This will overwrite the select statement that adds the files
            //  and photo ids to the results. According to the spec at http://jsonapi.org
            //  if you are specifying fields and you want child objects you need to
            //  add those to your field list.
            $this->query->select($params->fields->$table);
        }
        if (isset($params->fields->sort)) {
            //  Just adding a default for now.
        } else {
            $this->query->orderBy(Table::photos . ".created_at", "DESC");
        }
    }

    /**
     * Add hydrated related objects
     *
     * @param $params  object   the parameter object created by the BaseController
     * @throws BadRequestException
     */
    private function hasIncludes($params)
    {
        if (empty($params->include)) {
            return;
        }

        $filesJoin = null;
        $filesWhere = null;

        foreach ($params->include as $relation) {
            if (is_array($relation)) {
                switch ($relation[0]) {
                    case "files":
                        if (count($relation) > 2) {
                            throw new BadRequestException(implode(".", $relation) . " is not a valid relationship.");
                        }
                        $validTypes = array_keys(get_object_vars(config("phogra.fileTypes")));
                        if (!in_array($relation[1], $validTypes)) {
                            throw new BadRequestException(implode(".", $relation) . " is not a valid relationship.");
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
                        throw new BadRequestException(implode(".", $relation) . " is not a valid relationship.");
                        break;

                    default:
                        throw new BadRequestException(implode(".", $relation) . " is not a valid relationship.");

                }

                continue;
            }

            switch ($relation) {
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
    private function joinFiles()
    {
        $this->query->addSelect([
                                    "files.id as file_id", "type", "mimetype",
                                    "height", "width", "bytes", "hash", "top", "left",
                                    "files.created_at as file_created_at",
                                    "files.updated_at as file_updated_at"]
        );
        $this->query->orderBy(Table::photos . ".id");
        $this->query->orderBy(Table::files . ".type");

        $joinParams = new JoinParams();
        $joinParams->table = Table::files;
        $joinParams->on = [
            Table::files . ".photo_id",
            "=",
            Table::photos . ".id"
        ];

        $join = new Join($joinParams);


        //TODO: need the ability to turn this off
        if (true) {
            $join->addWhere(new WhereNull(Table::files . ".deleted_at"));
        }

        return $join;
    }
}
