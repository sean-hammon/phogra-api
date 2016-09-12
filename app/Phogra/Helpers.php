<?php
/**
 * User: Dez
 * Date: 9/11/2016
 */

namespace app\Phogra;

use Hashids;

class Helpers
{
	public static function extractIds($hashes) {
		$output = [];
		if (is_array($hashes)) {
			foreach ($hashes as $hash) {
				$decoded = Hashids::decode($hash);
				$output = array_merge($output, $decoded);

			}
		} else {
			$output = Hashids::decode($hashes);
		}

		return array_unique($output);
	}
}