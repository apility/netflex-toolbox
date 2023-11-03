<?php

namespace Netflex\Toolbox\Middleware;

use Illuminate\Http\Request;
use Netflex\Toolbox\Traits\CreateLocalUrl;

class AddTrailingSlash
{
    use CreateLocalUrl;

    public function handle(Request $request, \Closure $next, string $statusCode = '302') {

        if($request->method() === 'GET' && substr($_SERVER['PATH_INFO'], -1) !== '/') {
            return redirect()->to($this->fullAppendedUrl("/"), (int)$statusCode);
        }

        return $next($request);
    }

}