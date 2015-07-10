<?php

namespace App\Phogra\Response;

class BaseResponse
{
	public $links;
	public $data;
	public $included;

	public function __construct() {
		$this->links = (object)[
			'self' => $this->getSelf()
		];
	}

	public function send() {
		$responseObj = new \stdClass();

		//	Links go first
		$responseObj->links = $this->links;

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

		return $this->addHeaders()->json($responseObj);
	}

	/**
	 * Determines the self link from the request URI. If for some reason someone
	 * is using a script name, eg. /index.php/foo/bar, then the check for
	 * PATH_INFO will return the correct URI.
	 *
	 * @return string
	 */
	private function getSelf() {

		$self_link = $_SERVER['REQUEST_URI'];
		if (isset($_SERVER['PATH_INFO'])) {
			$self_link = $_SERVER['PATH_INFO'];
		}

		return $self_link;

	}

	private function addHeaders() {
		return response();
	}

}