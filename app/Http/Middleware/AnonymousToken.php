<?php

namespace App\Http\Middleware;

use App\Phogra\Eloquent\User;
use Closure;
use Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class AnonymousToken extends BaseMiddleware
{
	public function handle(Request $request, Closure $next) {

		$token = $this->auth->setRequest($request)->getToken();
		if ($token) {
			try {
				$user = $this->auth->authenticate($token);
			}
			catch(JWTException $e) {}
		}

		if (! isset($user)) {
			$email = uniqid('',true) . "@anonymous.com";
			$password = Hash::make(env('PH_ANON_PWD'));
			$user = User::create([
				"name" => "Anonymous User",
				"email" => $email,
				"password" => $password,
				"is_admin" => 0
			]);
		}
		$this->events->fire('tymon.jwt.valid', $user);

		return $next($request);
	}
}