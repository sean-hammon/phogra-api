<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Phogra\Exception\InvalidParameterException;
use App\Phogra\Exception\BadRequestException;
use App\Phogra\Response\BaseResponse;

class BaseController extends Controller {

	protected $request;
	protected $hasParams = false;
	protected $requestParams;
	protected $allowedParams = ['include','page','sort','filter','fields'];
	protected $warnings = [];

	public function __construct(Request $request) {
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

	protected function options() {
		$response = new BaseResponse();
		return $response->options();
	}

	private function processParams() {
		$get = $this->request->query();
		if(count($get)) {
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

	protected function getRequestBody() {
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

	private function processInclude($value) {
		$values = explode(',', $value);
		foreach ($values as $val) {
			if (strpos($val, '.') !== false) {
				$this->requestParams->include[] = explode('.', $val);
			} else {
				$this->requestParams->include[] = $val;
			}
		}
	}

	private function processPaging($value) {
		$this->requestParams->page = $value;
	}

	private function processSort($value) {
		$this->requestParams->sort = [$value];
	}

	private function processFilters($value) {

	}

	private function processFields($value) {
		$this->requestParams->fields[] = $value;
	}

	private function processEmpty($value) {
		if (class_basename($this) == 'GalleriesController') {
			$this->requestParams->empty = $value;
		} else {
			$warnings = app('Warnings');
			$warnings->addWarning('The empty parameter only applies to galleries and was ignored.');
		}
	}
}
