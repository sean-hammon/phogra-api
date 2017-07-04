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
	 * One of the image types defined in config/phogra.php
	 *
	 * @var string
	 */
	private $imageType;

	/**
	 * The sha hash generated from the image file itself.
	 *
	 * @var string
	 */
	private $hash;

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
     * @param $filePath  string  the location of the file to be processed
     *
     */
    public function __construct($filePath)
    {
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
    public function __destruct()
    {
        imagedestroy($this->imageResource);
    }


    /**
     * Set the photo id for save and replace operations.
     *
     * This used to be a parameter in the constructor. However, when uploading new
     * photos I wanted to make sure all the file processing was successful before
     * doing any database operations so I didn't have orphaned data to deal with.
     *
     * @param int $id
     */
    public function setPhotoId($id)
    {
        $this->photo_id = $id;
    }
    

    /**
     * @param string $imageType the image type to generate. Types defined in config/phogra.php
     * @param bool $replace Allow a matching hash to be over written, or remove an existing file
     *                      for a given type
     *
     * @return \App\Phogra\Eloquent\File App\Phogra\Eloquent\File
     *
     * @throws \App\Phogra\Exception\DuplicateFileException
     */
    public function make($imageType, $replace = false)
    {
        $this->hash = hash('sha256', file_get_contents($this->filePath));
	    $this->imageType = $imageType;

        if (!$replace) {
            $dupCheck = FileModel::where("hash", "=", $this->hash)->first();
            if ($dupCheck) {
                throw new DuplicateFileException("This image appears to already exist in the database: $this->hash.");
            }
        } else {
            $existingFile = FileModel::where("photo_id", "=", $this->photo_id)
                                        ->where("type", "=", $imageType);
            if ($existingFile) {
                // soft delete the file
                $existingFile->delete();
            }
        }
    }

    public function makeOrReplace($imageType) {
        $this->make($imageType, true);
    }

    public function storeFile() {
	    $data = [
		    'photo_id' => $this->photo_id,
		    'type' => $this->imageType,
		    'hash' => $this->hash,
		    'bytes' => filesize($this->filePath),
		    'height' => $this->originalHeight,
		    'width' => $this->originalWidth,
		    'mimetype' => $this->mime_type
	    ];

	    $fileRecord = FileModel::create($data);
	    $this->moveFile($this->filePath, $fileRecord->location());

	    return $fileRecord;

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