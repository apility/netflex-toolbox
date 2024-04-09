<?php

namespace Netflex\Toolbox\Events\Structures;

use Illuminate\Support\Facades\App;
use Netflex\Structure\Entry;
use Netflex\Structure\Model;

class EntryDeleted extends StructureChangeEvent
{

    public array $entry_data;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->entry_data = data_get($data, 'entry_data');
    }

    public function getEntry(): ?Model {
        if(App::bound('structure.' . $this->directory_id)) {
            return App::make('structure.' . $this->directory_id)->newFromBuilder($this->entry_data);
        }
        return (new Entry())->newFromBuilder($this->entry_data);
    }
}