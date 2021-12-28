<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Nagi\LaravelWopi\Support\RequestHelper;

class ValidateAccessToken
{
    public function handle(Request $request, Closure $next)
    {
        $accessToken = RequestHelper::parseAccessToken($request);

        abort_unless($accessToken === 'MyToken', 401);

        return $next($request);
    }
}
