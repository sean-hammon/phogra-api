<?php

namespace App\Phogra\Response;

use \DateTime;
use Storage;
use Hashids;
use App\Phogra\Eloquent\File;

class Image extends BaseResponse
{

    public function __construct($data)
    {
        parent::__construct();

        $this->data = new File((array)$data);
        $this->lastModified = new DateTime($data->updated_at);

        $this->etag = md5(json_encode($this->data));
    }

    public function send()
    {
        $incomingETag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;
        if ($incomingETag && $incomingETag == $this->etag) {
            return response("", 304);
        }

        $responseObj = new \stdClass();

        //	Links go first
        if (isset($this->links)) {
            $responseObj->links = $this->links;
        }

        //	Then warnings, if any
        $warnings = app('Warnings');
        if ($warnings->count()) {
            $responseObj->warnings = $warnings->getWarnings();
        }

        $headers = $this->addHeaders();
        $headers['Content-Type'] = $this->data->mimetype;

        $filepath = $this->data->location();

        return response()->make(readfile($filepath), $this->http_code, $headers);

    }
}