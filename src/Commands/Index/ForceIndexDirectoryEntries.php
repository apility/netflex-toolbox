<?php

namespace Netflex\Toolbox\Commands\Index;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Netflex\API\Facades\API;
use Netflex\Toolbox\Traits\IndexHelpers;

class ForceIndexDirectoryEntries extends Command
{
    use IndexHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:entry:index {id?} {{--directory-id=}} {{--dry-run}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index and/or update customer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        if ($this->argument('id')) {
            $this->dumpIf($this->indexEntry($this->argument('id')));
            return 0;
        }

        $directoryId = $this->option('directory-id');
        abort_unless($directoryId, 404, 'Directory id is missing');
        $this->info("Fetching all IDs for directory $directoryId");

        $ids = collect(API::get("builder/structures/$directoryId/ids"))->pluck('id');
        $this->withProgressBar($ids, function ($id) {
            $this->dumpIf($this->indexEntry($id));
        });

        return 0;
    }

    private function indexEntry($id)
    {
        $entryData = API::get('builder/structures/entry/' . $id);

        $newData = clone $entryData;

        $newData = app(Pipeline::class)
            ->send($newData)
            ->through(config('indexers.entry', []))
            ->thenReturn();

        $this->removeUnchangedFields($newData, $entryData);

        if ($this->option('dry-run')) {
            dump($newData);
        } else {
            if (sizeof(array_keys((array)$newData)) > 0) {
                $newData->revision_publish = 1;
                API::put("builder/structures/entry/$id", $newData);
            }
        }

        $data = API::put("elasticsearch/entry/$id");

        if ($data->message ?? null) {
            return json_decode($data->message) ?? $data->message;
        }

        return null;
    }


}
