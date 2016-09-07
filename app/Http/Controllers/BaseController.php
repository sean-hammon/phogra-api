<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Exception\BadRequestException;
use App\Phogra\Response\BaseResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    protected $request;
    protected $hasParams     = false;
    protected $requestParams;
    protected $allowedParams = ['include', 'page', 'sort', 'filter', 'fields'];
    protected $warnings      = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->requestParams = (object)[
            'include' => [],
            'page' => null,
            'sort' => null,
            'filter' => [],
            'fields' => [],
            'empty' => null
        ];
        $this->processParams();
    }


    protected function options()
    {
        $response = new BaseResponse();
        return $response->options();
    }


	protected function getRequestBody()
	{
		$json = $this->request->getContent();
		if (empty($json)) {
			throw new BadRequestException("No post body provided");
		}

		$data = json_decode($json, true);
		if (json_last_error() > 0) {
			throw new BadRequestException("JSON decode: " . json_last_error_msg());
		}

		return $data;
	}


    protected function getPutData() {

        $raw_data = $this->request->getContent();

        if( substr($raw_data, 0, 1) === "{") {

            $data["json"] = $this->getRequestBody();

        } else {

            //http://stackoverflow.com/questions/9464935/php-multipart-form-data-put-request
            $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

            // Fetch each part
            $parts = array_slice(explode($boundary, $raw_data), 1);
            $data = array();

            foreach ($parts as $part) {
                // If this is the last part, break
                if ($part == "--\r\n") {
                    break;
                }

                // Separate content from headers
                $part = ltrim($part, "\r\n");
                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
                // Parse the headers list
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    list($name, $value) = explode(':', $header);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }

                // Parse the Content-Disposition to get the field name, etc.
                if (isset($headers['content-disposition'])) {
                    $filename = null;
                    preg_match(
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                        $headers['content-disposition'],
                        $matches
                    );
                    list(, $type, $name) = $matches;
                    isset($matches[4]) and $filename = $matches[4];

                    // handle your fields here
                    if ($filename !== null) {

                        // must be a file upload
                        $tmpPath = config('phogra.photoTempDir') . DIRECTORY_SEPARATOR . $filename;
                        file_put_contents($tmpPath, $body);
                        $data[$name] = $tmpPath;
                        break;

                    } else {

                        // better be json or it will blow up later...
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }

        }

        return $data;
    }


	private function processParams()
	{
		$get = $this->request->query();
		if (count($get)) {
			$this->hasParams = true;
		}

		$incoming = array_keys($get);
		$disallowed = array_diff($incoming, $this->allowedParams);
		if (count($disallowed)) {
			throw new InvalidParameterException("Unrecognized parameters:" . implode(",", $disallowed));
		}
		foreach ($get as $key => $value) {
			switch ($key) {
				case 'include':
					$this->processInclude($value);
					break;

				case 'page':
					$this->processPaging($value);
					break;

				case 'sort':
					$this->processSort($value);
					break;

				case 'filter':
					$this->processFilters($value);
					break;

				case 'fields':
					$this->processFields($value);
					break;

				case 'empty':
					$this->processEmpty($value);
			}
		}
	}


    private function processInclude($value)
    {
        $values = explode(',', $value);
        foreach ($values as $val) {
            if (strpos($val, '.') !== false) {
                $this->requestParams->include[] = explode('.', $val);
            } else {
                $this->requestParams->include[] = $val;
            }
        }
    }


    private function processPaging($value)
    {
        $this->requestParams->page = $value;
    }


    private function processSort($value)
    {
        $this->requestParams->sort = [$value];
    }


    private function processFilters($value)
    {

    }


    private function processFields($value)
    {
        $this->requestParams->fields[] = $value;
    }


    private function processEmpty($value)
    {
        if (class_basename($this) == 'GalleriesController') {
            $this->requestParams->empty = $value;
        } else {
            $warnings = app('Warnings');
            $warnings->addWarning('The empty parameter only applies to galleries and was ignored.');
        }
    }
}
