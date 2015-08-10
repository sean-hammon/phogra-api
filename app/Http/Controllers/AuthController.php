<?php

namespace App\Http\Controllers;

use App\Phogra\Exception\UnauthorizedException;
use App\Phogra\Exception\UnknownException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
	public function authenticate(Request $request) {
		$authHeader = $request->header('Authorization');
		$authHeader = explode(" ", $authHeader);
		if ($authHeader[0] != 'Basic') {
			throw new UnauthorizedException();
		}

		$credentials = base64_decode($authHeader[1]);
		$credentials = explode(":", $credentials);
		$credentials = [
			"email" => $credentials[0],
			"password" => $credentials[1]
		];

		try {
			if (!$token = JWTAuth::attempt($credentials)) {
				throw new UnauthorizedException("Invalid login.");
			}
		} catch (JWTException $e) {
			throw new UnknownException('Could not create token:');
		}

		return response()->json(compact('token'));
	}

}