<?php

namespace Netflex\Toolbox\Middleware;

use Illuminate\Http\Request;
use Netflex\Toolbox\Traits\CreateLocalUrl;

class RemoveTrailingSlash
{
    use CreateLocalUrl;
    public function handle(Request $request, \Closure $next, string $statusCode = '302')
    {
        if ($request->method() === 'GET' && isset($_SERVER['PATH_INFO']) && substr($_SERVER['PATH_INFO'], -1) !== '/') {
            return redirect()->to($this->fullUrlWithTrim("/"), (int)$statusCode);
        }

        return $next($request);
    }
}