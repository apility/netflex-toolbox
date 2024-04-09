<?php

namespace Netflex\Toolbox\Events\Structures;

use Netflex\Structure\Entry;
use Netflex\Structure\Model;
use Netflex\Toolbox\Events\WebhookEvent;

abstract class StructureChangeEvent extends WebhookEvent implements \Stringable
{
    public string $type;
    public int $directory_id;
    public ?int $entry_id;

    public function getEntry(): ?Model
    {
        $id = $this->entry_id;
        /** @var Entry $entry */
        $entry = once(function () use ($id) {
            return Entry::where('id', $id)->first();
        });
        return $entry;
    }

    public function __construct(array $data)
    {
        parent::__construct($data);
        [$_, $this->directory_id, $this->type] = $this->getEventSegments(true);
        $entry_id = data_get($this->data, 'entry_id');
        $this->entry_id = !is_null($entry_id) ? (int)$entry_id : null;
    }

    public function toArray()
    {
        return [
            'type' => $this->type,
            'directory_id' => $this->directory_id,
            'entry_id' => $this->entry_id,
        ];
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}