<?php

namespace Netflex\Toolbox\Events\Customers;

use Netflex\Structure\Model;
use Netflex\Toolbox\Events\WebhookEvent;

abstract class CustomerChangeEvent extends WebhookEvent
{
    /**
     * @var mixed
     */
    public string $type;
    public ?int $customer_id;

    public function __construct($type, $customer_id)
    {
        $this->type = $type;
        $this->customer_id = !is_null($customer_id) ? (int)$customer_id : null;
    }

    public function getCustomer(): ?Model
    {
        $id = $this->customer_id;
        return once(function () use ($id) {
            return (config('toolbox-events.user-model', 'App\\Models\\User'))::find($id);
        });
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'directory_id' => $this->directory_id,
            'customer_id' => $this->customer_id,
        ];
    }

}