<?php

namespace App\Phogra\Response;

use \DateTime;
use Hashids;
use App\Phogra\Response\Item\User;

class Users extends BaseResponse
{

    public function __construct($data)
    {
        parent::__construct();

        if (is_array($data)) {
            $this->data = [];
            foreach ($data as $row) {
                $this->data[] = new User($row);
                $updated = new DateTime($row->updated_at);
                if ($updated > $this->lastModified) {
                    $this->lastModified = $updated;
                }
            }
        } else {
            $this->data = new User($data);
            $this->lastModified = new DateTime($data->updated_at);
        }

        $this->etag = md5(json_encode($this->data));

        $this->etag = md5(json_encode($this->data));
    }
}