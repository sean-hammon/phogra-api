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
		return $this->belongsToMany('App\Phogra\Eloquent\Photo', 'photo_files');
	}

	public function location()
	{
		preg_match('/([\w]{5})([\w]{5})(.*)/', $this->hash, $matches);
		array_shift($matches);
		$location = implode(DIRECTORY_SEPARATOR, $matches) . $this->fileExtension();

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