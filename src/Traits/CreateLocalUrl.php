<?php

namespace Netflex\Toolbox\Traits;

trait CreateLocalUrl
{


    /**
     *
     * Recreates the current url based on information from $_SERVER, but runs rtrim on the end of the path segment
     * in order to remove parts of the url
     *
     * @param string $trim
     * @return string
     */
    protected function fullUrlWithTrim(string $trim = ""): string
    {
        $scheme = request()->getScheme();
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim($_SERVER['PATH_INFO'], $trim);
        $query = !!($_SERVER['QUERY_STRING'] ?? null) ? ("?" . $_SERVER['QUERY_STRING']) : '';

        return "$scheme://$host$uri$query";
    }

    /**
     *
     * Recreates the current url, with a segment appended on the end of the url, while taking query parameters into account
     *
     * @param string $append
     * @return string
     */
    protected function fullAppendedUrl(string $append = ""): string
    {

        $scheme = request()->getScheme();
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['PATH_INFO'];
        $query = !!($_SERVER['QUERY_STRING'] ?? null) ? ("?" . $_SERVER['QUERY_STRING']) : '';

        return "$scheme://$host$uri$append$query";
    }
}