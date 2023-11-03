<?php

namespace Netflex\Toolbox\Commands\Orders;

use App\Models\Order;
use Illuminate\Console\Command;
use Netflex\Toolbox\Events\Orders\OrderManuallyPruned;

class PruneOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:orders:prune {--status=} {{--all-statuses}} {{--email-suffix=}} {{--data=*}} {{--limit=10000}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes orders';

    public function handle()
    {
        $orders = Order::limit($this->option('limit'))
        ->if($this->option('status'), fn($query) => $query->where('status', $this->option('status')))
        ->if($this->option('email-suffix'), fn($query) => $query->where('customer_mail', 'like', "*{$this->option('email-suffix')}"));

        foreach($this->option('data') as $value) {
            list($key, $value) = explode("=", $value, 2);
            $orders = $orders->where("data.$key", $value);
        }

        if($orders->getQuery(true) === "") {
            $this->error("Set atleast one search parameter");
            return -1;
        }

        $orders = $orders->get();

        $resolve = fn(Order $order) => [
            $order->id,
            $order->register ? $order->register->receipt_order_id : null,
            $order->secret ?? '',
            $order->status,
            $order->getOrderCustomerFirstname() . " " . $order->getOrderCustomerSurname(),
            $order->created,
            $order->updated,
            $order->order_total,
        ];

        $this->table(['id', 'register', 'secret', 'status', 'customer_name', 'created', 'updated', 'total'], $orders->map($resolve));

        if ($orders->count() && $this->confirm('Do you want to delete these orders?')) {
            $this->withProgressBar($orders, function(Order $order) {
                try {
                    $order->delete();
                    event(new OrderManuallyPruned($order));
                } catch (\Throwable $exception) {
                    $this->error($exception);
                }
            });
        }

        return 0;
    }
}