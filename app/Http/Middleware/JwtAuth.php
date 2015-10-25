<?php

namespace App\Http\Middleware;

use App\Phogra\Exception\UnauthorizedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class JwtAuth extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            throw new UnauthorizedException('Expired token.');
        } catch (JWTException $e) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        if (! $user) {
            throw new UnauthorizedException('Invalid credentials.');
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
