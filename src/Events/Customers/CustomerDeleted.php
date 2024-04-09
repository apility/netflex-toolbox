<?php

namespace Netflex\Toolbox\Events\Customers;

use Illuminate\Support\Facades\App;
use Netflex\Structure\Entry;
use Netflex\Structure\Model;
use Netflex\Toolbox\Events\Structures\StructureChangeEvent;

class CustomerDeleted extends CustomerChangeEvent
{
    public array $customer_data;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $customer_data = data_get($this->data, 'customer_data');
        $this->customer_data = $customer_data;
    }

    public function getCustomer(): ?Model {
        return (new (config('toolbox-events.user-model', 'App\\Models\\User')))->newFromBuilder($this->customer_data);
    }
}