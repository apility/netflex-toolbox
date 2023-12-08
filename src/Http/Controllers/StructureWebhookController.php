<?php

namespace Netflex\Toolbox\Http\Controllers;

use Netflex\Toolbox\Events\Structures\StructureEvent;

class StructureWebhookController
{
    public function process() {
        $event = StructureEvent::fromRequest();
        event($event);
        return "OK";
    }
}