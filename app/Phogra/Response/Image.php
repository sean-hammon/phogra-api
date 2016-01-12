<?php

namespace App\Phogra\Response;

use \DateTime;
use \Illuminate\Http\Response;
use Storage;
use Hashids;

use App\Phogra\Eloquent\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            //  Apparently Apache strips off CORS headers on 304 responses.
            //  See: http://blog.idetailaid.co.uk/cors-html5-application-cache-manifest-dont-work-together-neither-cors-apache/
            //  return response("", 304);
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
        $headers['Content-Disposition'] = "inline";
        $filepath = $this->data->location();

        $response = new StreamedResponse(function() use ($filepath) {
            readfile($filepath);
        }, $this->http_code, $headers);

        $response->send();
    }
}