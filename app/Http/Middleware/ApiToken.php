<?php

namespace app\Http\Middleware;

use App\Phogra\Eloquent\User;
use App\Phogra\Exception\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;

class ApiToken
{
	public function handle(Request $request, Closure $next) {

		if (!config('phogra.publicApi')) {
			$token = $request->header(config('phogra.apiTokenHeader'));
			if (!$token) {
				throw new UnauthorizedException('Token missing.');
			}
			$user = User::where('api_token', '=', $token)->first();
			if (!$user) {
				throw new UnauthorizedException('Invalid token.');
			}
		}

		return $next($request);
	}
}