<?php

namespace Netflex\Toolbox\Http\Controllers;

use Netflex\Toolbox\Events\Structures\StructureEvent;

class StructureWebhookController
{
    public function process()
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

        abort_unless($validDigest, 403);

        $event = StructureEvent::fromRequest();
        event($event);
        return "OK";
    }
}