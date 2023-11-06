<?php

namespace Netflex\Toolbox\UrlGenerators;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;

class LocaleDependentUrlGenerator extends UrlGenerator
{
    public function route($route, $parameters = [], $absolute = null)
    {
        $locale = App::getLocale();

        $namedRoute = $this->routes->getByName("$locale.$route") ?? $this->routes->getByName($route);

        if ($namedRoute) {
            return parent::route(
                $namedRoute->getName(),
                $parameters,
                $absolute ?? ($namedRoute->getDomain() !== request()->getHost()),
            );
        }

        $parameters = is_array($parameters) ? $parameters : [$parameters];
        $route = parent::route($route, array_merge($parameters, ['locale' => $locale]), $absolute);
        return parent::route($route, array_merge($parameters, ['locale' => $locale]), $absolute);
    }
}
