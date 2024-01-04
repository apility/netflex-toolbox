<?php

namespace Netflex\Toolbox\Events;

use Netflex\Toolbox\Events\Customers\CustomerCreated;
use Netflex\Toolbox\Events\Customers\CustomerDeleted;
use Netflex\Toolbox\Events\Customers\CustomerUpdated;
use Netflex\Toolbox\Events\Structures\EntryCreated;
use Netflex\Toolbox\Events\Structures\EntryDeleted;
use Netflex\Toolbox\Events\Structures\EntryUpdated;

abstract class WebhookEvent
{

    public static function fromRequest(): self
    {
        $split = explode(".", request()->get('event'));


        if ($split[0] === 'entry') {
            abort_unless(sizeof($split) == 3, 500);
            if ($split[2] === 'created') {
                $entry_id = request()->get('entry_id');
                abort_unless(!!$entry_id, 500, 'expected created webhook to have entry_id');
                return new EntryCreated($split[2], $split[1], $entry_id);
            }

            if ($split[2] === 'updated') {
                $entry_id = request()->get('entry_id');
                abort_unless(!!$entry_id, 500, 'expected updated webhook to have entry_id');
                return new EntryUpdated($split[2], $split[1], $entry_id);
            }

            if ($split[2] === 'deleted') {
                $entry_id = request()->get('entry_data');
                abort_unless(!!$entry_id, 500, 'expected created webhook to have entry_id');
                return new EntryDeleted($split[2], $split[1], $entry_id);
            }
        } else if ($split[0] === 'customer') {
            abort_unless(sizeof($split) == 2, 500);
            if ($split[1] === 'created') {
                $customer_id = request()->get('customer_id');
                abort_unless(!!$customer_id, 500, 'expected created webhook to have customer_id');
                return new CustomerCreated($split[1], $customer_id);
            }

            if ($split[1] === 'updated') {
                $customer_id = request()->get('customer_id');
                abort_unless(!!$customer_id, 500, 'expected updated webhook to have customer_id');
                return new CustomerUpdated($split[1], $customer_id);
            }

            if ($split[1] === 'deleted') {
                $customer_id = request()->get('customer_data');
                abort_unless(!!$customer_id, 500, 'expected created webhook to have customer_id');
                return new CustomerDeleted($split[1], $customer_id);
            }

        }

        throw new \Exception("Invalid part");
    }
}