<?php

namespace Netflex\Toolbox\Commands\Orders;

use App\Models\Order;
use Illuminate\Console\Command;

class PruneOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:orders:prune {--status=n} {{--email-suffix}} {{--data=*}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes order';

    public function handle()
    {
        $orders = dd($this->argument('status'));
        $order = Order::retrieveBySecret($this->argument('secret'));
        abort_unless($order, 404);

        $order->delete();

        return 0;
    }
}