<?php

namespace App\Phogra;

use App\Phogra\Eloquent\Gallery as GalleryModel;
use Illuminate\Database\Eloquent\Model;
use Mockery\CountValidator\Exception;

/**
 * Class Gallery
 * @package App\Phogra
 *
 * Still trying to decide if this is how I want to handle this.
 */
class Gallery
{
    private $rowData;

    public function __construct(array $data) {
        $this->rowData = $data;
        if (!isset($this->rowData['slug']) || empty($this->rowData['slug'])) {
            $this->rowData['slug'] = str_slug($this->rowData['title']);
        }
    }

    /**
     * @return object
     */
    public function create()
    {
        try {
            //  Returning an Eloquent model here feels oogy to me.
            $row = GalleryModel::create($this->rowData);
            $this->rowData = $row->getAttributes();
            return (object)$row;
        }
        catch(Exception $e) {
            // Do something if the insert fails
        }
    }
}