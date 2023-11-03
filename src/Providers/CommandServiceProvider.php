<?php

namespace Netflex\Toolbox\Providers;

use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Netflex\Toolbox\Commands\Orders\PruneOrders;

class CommandServiceProvider extends ArtisanServiceProvider
{

    protected $commands = [
    ];


    public function register()
    {
        parent::register();
        $this->commands([
            PruneOrders::class,
        ]);

    }

}