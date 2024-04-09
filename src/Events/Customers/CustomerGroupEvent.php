<?php

namespace Netflex\Toolbox\Events\Customers;

class CustomerGroupEvent extends CustomerChangeEvent
{
    public int $group_id;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->group_id = data_get($data, 'group_id');
    }
}