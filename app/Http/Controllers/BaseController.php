<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Phogra\Exception\InvalidParameterException;

class BaseController extends Controller {

	protected $requestParams;
	private $allowedParams = ['include','page','sort','filter','fields'];

	public function __construct(Request $request) {
		$this->requestParams = (object)[
			'include' => [],
			'page' => null,
			'sort' => null,
			'filter' => [],
			'fields' => []
		];
		$this->processParams($request);
	}


	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	private function processParams(Request $request) {
		$get = $request->all();
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
}
