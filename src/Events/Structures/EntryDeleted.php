<?php

namespace Netflex\Toolbox\Events\Structures;

use Illuminate\Support\Facades\App;
use Netflex\Structure\Entry;
use Netflex\Structure\Model;

class EntryDeleted extends StructureEvent
{

    public array $entry_data;

    public function __construct($type, $directory_id, array $entry_data)
    {
        $this->entry_data = $entry_data;
        parent::__construct($type, $directory_id, $entry_data['id'] ?? -1);
    }

    public function getEntry(): ?Model {
        if(App::bound('structure.' . $this->directory_id)) {
            return App::make('structure.' . $this->directory_id)->newFromBuilder($this->entry_data);
        }
        return (new Entry())->newFromBuilder($this->entry_data);
    }
}