<?php

namespace Netflex\Toolbox\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Netflex\Toolbox\Events\Customers\CustomerAddedToGroup;
use Netflex\Toolbox\Events\Customers\CustomerCreated;
use Netflex\Toolbox\Events\Customers\CustomerDeleted;
use Netflex\Toolbox\Events\Customers\CustomerRemovedFromGroup;
use Netflex\Toolbox\Events\Customers\CustomerRemovedFromGroupEvent;
use Netflex\Toolbox\Events\Customers\CustomerUpdated;
use Netflex\Toolbox\Events\Structures\EntryCreated;
use Netflex\Toolbox\Events\Structures\EntryDeleted;
use Netflex\Toolbox\Events\Structures\EntryUpdated;
use Netflex\Toolbox\Events\Structures\StructureChangeEvent;
use Netflex\Toolbox\Events\WebhookEvent;

class StructureWebhookController
{
    public function process(Request $request)
    {

        $messageId = request()->header('x-message-id');
        $digest = request()->header('x-message-digest');

        $potentialKeys = collect(config('api.connections', []))
            ->pluck('privateKey');
        $potentialKeys->add(config('api.privateKey'));

        $validDigest = $potentialKeys
                ->filter()
                ->filter(fn($key) => hash('sha256', $messageId . $key) === $digest)
                ->count() > 0;

        abort_unless($validDigest || App::environment() === 'local', 403);

        $events = [
            'entry.*.created' => EntryCreated::class,
            'entry.*.updated' => EntryUpdated::class,
            'entry.*.deleted' => EntryDeleted::class,

            'customer.created' => CustomerCreated::class,
            'customer.updated' => CustomerUpdated::class,
            'customer.deleted' => CustomerDeleted::class,
            'customer.group-added' => CustomerAddedToGroup::class,
            'customer.group-removed' => CustomerRemovedFromGroup::class,

        ];

        $eventType = $request->get('event');

        foreach($events as $event => $class) {
            if(Str::is($event, $eventType)) {
                event(new $class($request->all()));
                return "OK";
            }
        }

        event(new WebhookEvent($request->all()));
        return "OK";
    }
}