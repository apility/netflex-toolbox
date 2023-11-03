<?php

namespace Netflex\Toolbox\Providers;

use Illuminate\Support\Facades\Artisan;
use Netflex\Toolbox\Middleware\AddTrailingSlash;
use Netflex\Toolbox\Middleware\RemoveTrailingSlash;

class ToolboxProvider extends \Illuminate\Support\ServiceProvider
{

    public function register()
    {
        $this->registerTrailingSlashHelpers();
        $this->registerOrderCommands();
    }

    public function boot()
    {

    }


    public function registerTrailingSlashHelpers()
    {
        app()->bind('add-slash', AddTrailingSlash::class);
        app()->bind('remove-slash', RemoveTrailingSlash::class);
    }

    private function registerOrderCommands()
    {
    }

}
