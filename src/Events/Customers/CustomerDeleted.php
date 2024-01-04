<?php

namespace Netflex\Toolbox\Events\Customers;

use Illuminate\Support\Facades\App;
use Netflex\Structure\Entry;
use Netflex\Structure\Model;
use Netflex\Toolbox\Events\Structures\StructureChangeEvent;

class CustomerDeleted extends CustomerChangeEvent
{
    public array $customer_data;

    public function __construct($type, array $customer_data)
    {
        $this->customer_data = $customer_data;
        parent::__construct($type, $customer_data['id'] ?? -1);
    }

    public function getCustomer(): ?Model {
        return (new (config('toolbox-events.user-model', 'App\\Models\\User')))->newFromBuilder($this->customer_data);
    }
}