<?php

namespace app\Phogra\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 */
class File extends Model
{
	protected $table = "files";
	protected $fillable = ['hash','height','width','bytes','type','mimetype'];

	public function photos()
	{
		return $this->belongsTo('App\Phogra\Eloquent\Photo');
	}

	public function location()
	{
		preg_match('/([\w]{2})([\w]{2})(.*)/', $this->hash, $matches);
		array_shift($matches);
		$location = config("phogra.photoDir") . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $matches) . $this->fileExtension();

		return $location;
	}

	public function fileExtension()
	{
		switch ($this->mimetype) {
			case "image/jpeg":
				return ".jpg";

			case "image/png":
				return ".png";

			default:
				return ".unk";
		}
	}
}