<?php

namespace App\Phogra\Response;

use \DateTime;
use Auth;
use JWTAuth;
use JWTFactory;

class BaseResponse
{
    public $links;
    public $data;
    public $http_code = 200;

    protected $allowedHttpVerbs = 'GET, HEAD, OPTIONS';
    protected $lastModified;
    protected $etag;

    public function __construct()
    {
        $this->lastModified = new DateTime('1970-01-01');
        $this->links = (object)[
            'self' => $this->getSelf()
        ];
    }

    /**
     * @return \Illuminate\Http\Response
     */
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

        //	Now the data
        $responseObj->data = $this->data;

        //	Now any included data, if any
        if (isset($this->included)) {
            $responseObj->included = $this->included;
        }

        return response()->json($responseObj, $this->http_code, $this->addHeaders(), $this->jsonOptions());
    }

    public function options()
    {
        return response("Phogra API", 200, $this->addHeaders());
    }

    protected function addHeaders()
    {

        $allowedDomains = config("phogra.allowedDomains");
        $ssl = !empty($_SERVER['HTTPS']) ? "s" : '';
        $requestHost = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $requestDomain = "";

        if ($allowedDomains[0] == "*" || in_array($allowedDomains, $requestHost)) {
            $requestDomain = $requestHost;
        }

        $headers = [
            'Accept' => 'application/json',
            'Access-Control-Allow-Headers' => 'Content-Type,Authorization',
            //	30 days
            'Access-Control-Max-Age' => 30 * 24 * 60 * 60,
            'Access-Control-Allow-Origin' => $requestDomain,
            'Access-Control-Allow-Methods' => $this->allowedHttpVerbs,
	        'Access-Control-Expose-Headers' => 'X-Phogra-Token'
        ];

        $token = $this->buildJWT();
        if ($token !== false) {
            $key = config('phogra.apiTokenHeader');
            $headers[$key] = $token;
        }

        if (isset($this->lastModified)) {
            $headers['ETag'] = $this->etag;
            $headers['Last-Modified'] = gmdate("D, d M Y H:i:s", $this->lastModified->getTimestamp()) . " GMT";
        }

        return $headers;
    }

    /**
     * Determines the self link from the request URI. If for some reason someone
     * is using a script name, eg. /index.php/foo/bar, then the check for
     * PATH_INFO will return the correct URI.
     *
     * @return string
     */
    private function getSelf()
    {

        $self_link = $_SERVER['REQUEST_URI'];
        if (isset($_SERVER['PATH_INFO'])) {
            $self_link = $_SERVER['PATH_INFO'];
        }

        return $self_link;

    }

    /**
     * Build the JWT based on the user type. Anonymous users need a manual
     * build to keep the TTL long. Registered users use the default
     * functionality with a shorter TTL.
     */
    private function buildJWT()
    {
        $user = Auth::user();
        if (!isset($user)) {
            return false;
        }

        if (strlen($user->email) == 37 && substr($user->email, -14) == '@anonymous.com') {
            $payload = JWTFactory::setTTL(365*24*60)->sub($user->id)->make();
            $token = JWTAuth::encode($payload);
        } else {
            $token = JWTAuth::fromUser($user);
        }

        return $token;

    }

    private function jsonOptions()
    {
        return JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
    }

}