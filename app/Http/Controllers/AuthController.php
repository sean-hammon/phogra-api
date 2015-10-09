<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Phogra\Exception\UnauthorizedException;
use App\Phogra\Exception\UnknownException;
use App\Phogra\Eloquent\User;
use App\Phogra\Response\Users as UserResponse;

class AuthController extends BaseController
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

        $user = User::where('email', '=', $credentials['email'])->first();

		$user->token = $token;
        $response = new UserResponse($user);
		return $response->send();
	}

}