<?php
namespace App\Phogra\File;

use App\Phogra\Eloquent\File as FileModel;
use App\Phogra\Exception\DuplicateFileException;

class Processor
{
	/**
	 * The id of the photo to which this file belongs
	 *
	 * @var int
	 */
	private $photo_id;

	/**
	 * The file path of the file to be processed
	 *
	 * @var string
	 */
	private $filePath;

	/**
	 * The mime type of the file returned by FileInfo
	 *
	 * @var string
	 */
	private $mime_type;

	/**
	 * An image resource handle to the original image
	 *
	 * @var resource
	 */
	private $imageResource;

	/**
	 * The width of the original image.
	 *
	 * @var int
	 */
	private $originalWidth;

	/**
	 * The height of the original image.
	 *
	 * @var int
	 */
	private $originalHeight;

	/**
	 * Process a new file: create the database record and move the file to its home
	 * on the file system.
	 *
	 * @param $photo_id  int     the photo to which this file belongs
	 * @param $filePath  string  the location of the file to be processed
	 *
	 */
	public function __construct($photo_id, $filePath)
	{
		$this->photo_id = $photo_id;
		$this->filePath = $filePath;
		$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
		$this->mime_type = $fileInfo->file($this->filePath);
		switch ($this->mime_type) {
			case "image/jpeg":
				$this->imageResource = imagecreatefromjpeg($this->filePath);
				break;

			case "image/png":
				$this->imageResource = imagecreatefrompng($this->filePath);
				break;
		}

		$this->originalWidth = imagesx($this->imageResource);
		$this->originalHeight = imagesy($this->imageResource);
	}

	/**
	 * Make sure we're releasing the memory associate with this image resource
	 */
	public function __destruct() {
		imagedestroy($this->imageResource);
	}

	/**
	 * @param  $imageType string  the image type to generate. Types defined in config/phogra.php
	 * @return FileModel  App\Phogra\Eloquent\File
	 *
	 * @throws DuplicateFileException
	 */
	public function make($imageType)
	{
		$tmpPath = config('phogra.photoTempDir') . '/tmp_' . bin2hex(openssl_random_pseudo_bytes(16));

		$typeConfig = config("phogra.fileTypes");
		$type = $typeConfig->$imageType;

		if (is_null($type->width) && is_null($type->height)) {

			//	We're just going to copy the original image and do not need make any
			//	modifications.

			$modified = imagecreatetruecolor($this->originalWidth, $this->originalHeight);
			imagecopy($modified, $this->imageResource, 0, 0, 0, 0, $this->originalWidth, $this->originalHeight);

		} elseif (isset($type->width) && isset($type->height)) {

			//	If both dimensions are given, we know we need to crop.

			$modified = $this->cropImage($this->imageResource, $type->width, $type->height);

		} elseif (is_null($type->width) ) {
			if ($this->originalHeight >= $this->originalWidth) {

				//	If width is null and we have a portrait image, just scale.

				$modified = $this->resizeImage($this->imageResource, $type->width, $type->height);

			} else {

				//	Otherwise we're going to crop it to a portrait image

				$croppedWidth = intval($type->height * $this->originalHeight / $this->originalWidth);
				$modified = $this->cropImage($this->imageResource, $croppedWidth, $type->height);
			}
		} elseif (is_null($type->height)) {
			if ($this->originalWidth >= $this->originalHeight) {

				//	Height is null and we have a landscape image. Just scale.

				$modified = $this->resizeImage($this->imageResource, $type->width, $type->height);
			} else {

				//	Otherwise we're going to crop it to a portrait image

				$croppedHeight = intval($type->width * $this->originalWidth / $this->originalHeight);
				$modified = $this->cropImage($this->imageResource, $type->width, $croppedHeight);
			}
		}

		$this->writeTempFile($modified, $tmpPath);
		$hash = hash('sha256', file_get_contents($tmpPath));
		$dupCheck = FileModel::where("hash", "=", $hash)->first();
		if ($dupCheck) {
			throw new DuplicateFileException("This image appears to already exist in the database.");
		}
		$data = [
			'photo_id' => $this->photo_id,
			'type'     => $imageType,
			'hash'     => $hash,
			'bytes'    => filesize($tmpPath),
			'height'   => imagesy($modified),
			'width'    => imagesx($modified),
			'mimetype' => $this->mime_type
		];
		$fileRecord = FileModel::create($data);
		$this->moveFile($tmpPath, $fileRecord->location());

		imagedestroy($modified);
		//unlink($tmpPath);
//		unlink($this->filePath);

		return $fileRecord;
	}

	private function resizeImage($image, $width, $height) {
		$original = (object)[
			'width' => imagesx($image),
			'height' => imagesy($image)
		];
		$modified = (object)[
			'width' => $width,
			'height' => $height
		];
		$ratio = $original->width/$original->height;

		if (is_null($modified->width)) {
			$modified->width = intval($modified->height * $ratio);
		}

		if (is_null($modified->height)) {
			$modified->height = intval($modified->width / $ratio);
		}

		$copy = imagecreatetruecolor($modified->width, $modified->height);
		imagecopyresampled($copy, $image, 0, 0, 0, 0, $modified->width, $modified->height, $original->width, $original->height);

		return $copy;
	}

	private function cropImage($image, $width, $height) {
		$original = (object)[
			'width' => imagesx($image),
			'height' => imagesy($image)
		];
		$resize = (object)[
			'width' => $original->width <= $original->height ? $width : null,
			'height' => $original->height <= $original->width ? $height : null,
		];
		$resized = $this->resizeImage($image, $resize->width, $resize->height);

		$newX = intval((imagesx($resized) - $width) / 2);
		$newY = intval((imagesy($resized) - $height) / 2);
		$copy = imagecreatetruecolor($width, $height);
		imagecopy($copy, $resized, 0, 0, $newX, $newY, $width, $height);

		imagedestroy($resized);
		return $copy;
	}

	private function writeTempFile($image, $path, $quality = null) {
		switch ($this->mime_type) {
			case "image/jpeg":
				$quality = is_null($quality) ? 75 : $quality;
				imagejpeg($image, $path, $quality);
				break;

			case "image/png":
				$quality = is_null($quality) ? 3 : $quality;
				imagepng($image, $path, $quality);
				break;
		}

	}

	/**
	 * Make sure the directory exists and then move the file.
	 *
	 * @param string $oldPath
	 * @param string $newPath
	 */
	private function moveFile($oldPath, $newPath)
	{
		$exploded = explode(DIRECTORY_SEPARATOR, $newPath);
		array_pop($exploded);
		$path = implode(DIRECTORY_SEPARATOR, $exploded);

		if (!file_exists($path)) {
			mkdir($path, 0775, true);
		}

		rename($oldPath, $newPath);
	}
}