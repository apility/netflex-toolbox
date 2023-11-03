<?php

namespace Netflex\Toolbox\Middleware;

use Illuminate\Http\Request;

class RemoveTrailingSlash
{
    public function handle(Request $request, \Closure $next, string $statusCode = '302') {


        if($request->method() === 'GET' && substr($request->path(), -1) === '/') {
            return redirect()->to(rtrim($request->url(), "/"), (int)$statusCode);
        }

        return $next($request);
    }
}