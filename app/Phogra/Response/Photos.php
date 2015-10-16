<?php

namespace App\Phogra\Response;

use \DateTime;
use Hashids;
use App\Phogra\Response\Item\Photo as ResponseItem;
use App\Phogra\Eloquent\Photo as PhotoModel;

class Photos extends BaseResponse
{

    public function __construct($data)
    {
        parent::__construct();

        if ($data instanceof PhotoModel) {
            $this->data = new ResponseItem($data);
            $this->lastModified = new DateTime($data->updated_at);
            $files = $data->files()->get();
            foreach ($files as $f) {
                $this->data->addFile($f);
            }
        } elseif (is_array($data)) {
            $this->data = [];
            $current = null;
            foreach ($data as $row) {
                if (!isset($current) || Hashids::encode($row->id) != $current->id) {
                    $current = new ResponseItem($row);
                    $this->data[] = $current;
                }
                $updated = new DateTime($row->updated_at);
                if ($updated > $this->lastModified) {
                    $this->lastModified = $updated;
                }

                if (isset($row->file_id)) {
                    $current->addFile($row);
                }
            }
        } else {
            $this->data = new ResponseItem($data);
            $this->lastModified = new DateTime($data->updated_at);
            if (isset($data->file_id)) {
                $this->data->addFile($data);
            }
        }

        $this->etag = md5(json_encode($this->data));
    }
}