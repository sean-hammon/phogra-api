<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Phogra\Exception\InvalidParameterException;

class BaseController extends Controller {

	protected $request;
	protected $hasParams = false;
	protected $requestParams;
	protected $allowedParams = ['include','page','sort','filter','fields','empty'];
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
		$response = response('', 200)
			->header('Accept', 'application/json')
			->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
			->header('Access-Control-Allow-Headers', 'X-Phogra-Token')
			//	30 days
			->header('Access-Control-Max-Age', 30 * 24 * 60 * 60);

		$requestDomain = $this->request->server('HTTP_HOST');
		$allowedDomains = config('phogra.allowedDomains');
		if ($allowedDomains[0] === "*" || in_array($requestDomain, $allowedDomains)) {
			$response->header('Access-Control-Allow-Origin', $requestDomain);
		}

		return $response;
	}

	private function processParams() {
		$get = $this->request->all();
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

	private function processInclude($value) {
		$pairs = explode(',', $value);
		foreach ($pairs as $p) {
			$this->requestParams->include[] = explode('.', $p);
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
