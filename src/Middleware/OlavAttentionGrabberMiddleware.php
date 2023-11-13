<?php

namespace Netflex\Toolbox\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class OlavAttentionGrabberMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->boolean('_grab_attention')) {
            Cache::put('grab-attention', $request->boolean('_grab_attention'));
        }

        if (Cache::get('grab-attention', false) && Str::contains($request->header('User-Agent'), 'FreshpingBot')) {
            return response('502: Bad Gateway', 502);
        }

        return $next($request);
    }
}