<?php

namespace Netflex\Toolbox\Events\Structures;

use Netflex\Structure\Entry;
use Netflex\Structure\Model;

abstract class StructureEvent implements \Stringable
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

    public function __construct($type, $directory_id, $entry_id)
    {
        $this->type = $type;
        $this->directory_id = $directory_id;
        $this->entry_id = !is_null($entry_id) ? (int)$entry_id : null;
    }


    public static function fromRequest(): self
    {
        $split = explode(".", request()->get('event'));
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