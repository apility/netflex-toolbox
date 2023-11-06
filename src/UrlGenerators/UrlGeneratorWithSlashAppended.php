<?php

namespace Netflex\Toolbox\UrlGenerators;

use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Routing\UrlGenerator;

class UrlGeneratorWithSlashAppended extends UrlGenerator
{
    private UrlGeneratorContract $wrapped;

    public function __construct(UrlGeneratorContract $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    public function current()
    {
        return $this->wrapped->current();
    }

    public function previous($fallback = false)
    {
        return $this->wrapped->previous($fallback);
    }

    public function to($path, $extra = [], $secure = null)
    {
        return $this->wrapped->to($path, $extra, $secure);
    }

    public function secure($path, $parameters = [])
    {
        return $this->wrapped->secure($path, $parameters);
    }

    public function asset($path, $secure = null)
    {
        return $this->wrapped->asset($path, $secure);
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        return $this->appendSlash($this->wrapped->route($name, $parameters, $absolute));
    }

    public function action($action, $parameters = [], $absolute = true)
    {
        return $this->appendSlash($this->wrapped->action($action, $parameters, $absolute));
    }

    public function setRootControllerNamespace($rootNamespace)
    {
        return $this->wrapped->setRootControllerNamespace($rootNamespace);
    }


    private function appendSlash(string $url) {
        $info = optional(parse_url($url));

        $scheme = $info['scheme'] ? $info['scheme'] . "://" : '';
        $host = $info['host'] ?? '';
        $path = $info['path'] ? (rtrim($info['path'], '/') . "/") : '';
        $query = $info['query'] ? "?{$info['query']}" : '';

        return "$scheme$host$path$query";
    }

    public function __call($name, $arguments)
    {
        $this->wrapped->{$name}(...$arguments);
    }
}
