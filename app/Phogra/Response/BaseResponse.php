<?php

namespace App\Phogra\Response;

class BaseResponse
{
	public $links;
	public $data;
	public $included;

	public function __construct($rows) {

		$this->links = (object)[
			'self' => $this->getSelf()
		];
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

}