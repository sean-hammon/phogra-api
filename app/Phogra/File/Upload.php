<?php
namespace App\Phogra\File;

use Config;
use Intervention\Image\ImageManagerStatic as Image;

class Upload
{
	/**
	 * The file records created during upload processing.
	 *
	 * @var File_[]
	 */
	private $records;
	/**
	 * The file path of the uploaded file
	 *
	 * @var string
	 */
	private $filPath;

	/**
	 * Process a file upload: create the database record and move the file to its home
	 * on the file system.
	 *
	 * @param string $photo_id
	 * @param string $filePath
	 * @param string $type
	 *
	 * @throws \Phogra\File\DuplicateFileException
	 * @throws \Phogra\File\UnknownFileTypeException
	 */
	public function __construct($photo_id, $filePath, $type = 'original')
	{
		$hash = hash('sha256', file_get_contents($filePath));
		$dupCheck = File_::where( "hash", "=", $hash)->first();
		if ($dupCheck) {
			throw new DuplicateFileException("This image appears to already exist in the database.");
		}

		$this->filePath = $filePath;
		$this->photo_id = $photo_id;
		switch ($type) {
			case 'original':
				$this->processOriginal();
				$this->makeThumb();
				break;

			case 'hires':
				$this->processHires();
				$this->makeThumb();
				break;

			case 'lowres':
				$this->processLowres();
				break;

			case "thumb":
				$this->processThumb();
				break;

			default:
				throw new UnknownFileTypeException();
		}

		$image = Image::make($filePath);
		$fileInfo = new \finfo(FILEINFO_MIME_TYPE);
		$data = [
			'hash' => $hash,
			'bytes' => $image->filesize(),
			'height' => $image->height(),
			'width' => $image->width(),
			'mimetype' => $fileInfo->file($filePath)
		];

		$this->fileRecord = File_::create($data);
		$this->moveFile($filePath, $this->fileRecord->location());

		$thumbPath = Config::get('phogra.tempPhotoDir') . '/th_' . bin2hex(openssl_random_pseudo_bytes(8)) . $this->fileRecord->fileExtension();
		$thumbSize = Config::get('phogra.thumbSize');
		$image->resize(null, $thumbSize, function($constraint){
			$constraint->aspectRatio();
		});
		$image->crop($thumbSize, $thumbSize);
		$image->save($thumbPath);
		$data = [
			'hash' => hash('sha256', file_get_contents($thumbPath)),
			'bytes' => $image->filesize(),
			'height' => $image->height(),
			'width' => $image->width(),
			'mimetype' => $fileInfo->file($filePath)
		];
		$this->thumbRecord = File_::create($data);
		$this->moveFile($thumbPath, $this->thumbRecord->location());
		//preg_match('/([\w]{3})([\w]{3})(.*)/', $data['hash'], $matches);
		//$newHome = base_path() . '/' . 'photos/' . implode('/',$matches);

	}

	/**
	 * Return the final location of the uploaded file.
	 *
	 * @return string
	 */
	public function getPhotoFilePath(){
		return Config::get('phogra.photoDir') . DIRECTORY_SEPARATOR . $this->fileRecord->location();
	}

	/**
	 * Return the final location of the generated thumb file.
	 *
	 * @return string
	 */
	public function getThumbFilePath(){
		return Config::get('phogra.photoDir') . DIRECTORY_SEPARATOR . $this->thumbRecord->location();
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