<?php

namespace Netflex\Toolbox\Providers;

use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Netflex\Toolbox\Commands\Index\ForceIndexCustomer;
use Netflex\Toolbox\Commands\Index\ForceIndexDirectoryEntries;
use Netflex\Toolbox\Commands\Index\ForceIndexNewsletter;
use Netflex\Toolbox\Commands\Index\ForceIndexSignups;
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
            ForceIndexCustomer::class,
            ForceIndexNewsletter::class,
            ForceIndexDirectoryEntries::class,
            ForceIndexSignups::class,
        ]);

    }

}
