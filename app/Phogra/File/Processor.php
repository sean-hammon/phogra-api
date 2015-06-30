<?php
namespace App\Phogra\File;

use Config;
use Intervention\Image\ImageManagerStatic as Image;

class Processor
{
	/**
	 * The file path of the uploaded file
	 *
	 * @var string
	 */
	private $filePath;
	/**
	 * The Eloquent model for files that owns these files
	 *
	 * @var array
	 */
	private $fileRecords;
	/**
	 * The mime type of the file returned by FileInfo
	 *
	 * @var string
	 */
	private $mime_type;

	/**
	 * Process a new file: create the database record and move the file to its home
	 * on the file system.
	 *
	 * @param string $filePath
	 *
	 * @throws DuplicateFileException
	 */
	public function __construct($filePath)
	{
		$hash = hash('sha256', file_get_contents($filePath));
		$dupCheck = File_::where("hash", "=", $hash)->first();
		if ($dupCheck) {
			throw new DuplicateFileException("This image appears to already exist in the database.");
		}

		$this->filePath = $filePath;
		$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
		$this->mime_type = $fileInfo->file($this->filePath);
	}

	public function processOriginal(array $needs = ['thumb'])
	{
		$this->autoGenerate($needs);
		$this->process($this->filePath, 'original');

		return $this->fileRecords;
	}

	public function processHires(array $needs = ['thumb'])
	{
		$this->autoGenerate($needs);
		$this->process($this->filePath, 'hires');

		return $this->fileRecords;
	}

	public function processLowres(array $needs = ['thumb'])
	{
		$this->autoGenerate($needs);
		$this->process($this->filePath, 'lowres');

		return $this->fileRecords;
	}

	public function processThumb()
	{
		$this->process($this->filePath, 'thumb');

		return $this->fileRecords;
	}

	/**
	 * Loop through the file types that need to be auto-generated.
	 *
	 * @param array $needs
	 */
	private function autoGenerate(array $needs)
	{
		foreach ($needs as $type) {
			$file_path = $this->make($type);
			$this->process($file_path, $type);
		}

	}

	private function make($type)
	{
		$tmpPath = Config::get('phogra.tempPhotoDir') . '/tmp_' . bin2hex(openssl_random_pseudo_bytes(16));
		$size = Config::get("phogra.sizes.{$type}");
		$image = Image::make($this->filePath);
		$image->resize(null, $size['height'], function($constraint){
			$constraint->aspectRatio();
		});
		$image->crop($size['width'], $size['height']);
		$image->save($tmpPath);

		return $tmpPath;
	}

	private function process($file_path, $file_type)
	{
		$hash = hash('sha256', file_get_contents($file_path));
		$image = Image::make($file_path);
		$data = [
			'type'     => $file_type,
			'hash'     => $hash,
			'bytes'    => $image->filesize(),
			'height'   => $image->height(),
			'width'    => $image->width(),
			'mimetype' => $this->mime_type
		];

		$this->fileRecords[] = new File_($data);
		$this->moveFile($this->filePath, $this->fileRecord->location());
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
		$filename = array_pop($exploded);
		$path = Config::get('phogra.photoDir') . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $exploded);

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		rename($oldPath, $path . DIRECTORY_SEPARATOR . $filename);
	}
}