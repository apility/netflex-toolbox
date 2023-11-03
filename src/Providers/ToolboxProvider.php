<?php

namespace Netflex\Toolbox\Providers;

use Netflex\Toolbox\Middleware\AddTrailingSlash;
use Netflex\Toolbox\Middleware\RemoveTrailingSlash;

class ToolboxProvider extends \Illuminate\Support\ServiceProvider
{

    public function register()
    {


        $this->registerTrailingSlashHelpers();

    }

    public function boot()
    {
        $this->bootOrderCommandConfigs();
    }


    public function registerTrailingSlashHelpers()
    {
        app()->bind('add-slash', AddTrailingSlash::class);
        app()->bind('remove-slash', RemoveTrailingSlash::class);
    }

    private function bootOrderCommandConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/indexers.php", "indexers");

        $this->publishes([
            __DIR__ . "/../../config/indexers.php" => $this->app->configPath('indexers.php'),
        ], 'toolbox-config',
        );

    }

}
