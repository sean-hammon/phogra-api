<?php

/**
 * Class File_
 *
 * File_ because File conflicts with an alias for a filesystem class.
 * Hopefully this will be fixed with 5.0.
 */
class File_ extends Eloquent
{
	protected $table = "files";
	protected $fillable = ['hash','height','width','bytes','type','mimetype'];

	public function photos()
	{
		return $this->belongsToMany('Photo', 'photo_files');
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