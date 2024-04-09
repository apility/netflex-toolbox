<?php

namespace Netflex\Toolbox\Events;

use Illuminate\Support\Str;
use Netflex\Toolbox\Events\Customers\CustomerCreated;
use Netflex\Toolbox\Events\Customers\CustomerDeleted;
use Netflex\Toolbox\Events\Customers\CustomerUpdated;
use Netflex\Toolbox\Events\Structures\EntryCreated;
use Netflex\Toolbox\Events\Structures\EntryDeleted;
use Netflex\Toolbox\Events\Structures\EntryUpdated;

class WebhookEvent
{

    public array $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }


    protected function getEventSegments(bool $hasDirectoryId = false): array {
        $eventString = $this->data['event'];

        return explode(".", $eventString, $hasDirectoryId ? 3 : 2);

    }

    public function get(string $key, $default = null) {
        return data_get($this->data, $key, $default);
    }
}