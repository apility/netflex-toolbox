<?php

namespace Netflex\Toolbox\Commands\Index;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Netflex\API\Facades\API;
use Netflex\Query\Facades\Search;

class ForceIndexSignups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:signup:index {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index one or more newsletters by id. Be aware, if you enter ids that does not exists, they will still be indexed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        if ($id = $this->argument('id')) {
            $allEntries = collect([API::get('relations/signups/' . $id)]);
        } else {
            $dbAll = collect(API::get('relations/signups'));
            $inDatabase = $dbAll->pluck('id');

            $inElasticSearch = collect();

            $id = 0;
            do {
                $results = Search::relation('signup')
                    ->ignorePublishingStatus()
                    ->where('id', '>', $id)
                    ->field('id')
                    ->orderBy('id', 'asc')
                    ->limit(10000)
                    ->get()
                    ->pluck('id');


                $inElasticSearch = $inElasticSearch->merge($results);
                $id = $results->max();

            } while ($results->count() > 0);
            $allEntries = $dbAll->reject(fn($object) => $inElasticSearch->contains($object->id));

        }


        $this->withProgressBar($allEntries, function ($data) {

            $newData = clone $data;

            $newData = app(Pipeline::class)
                ->send($newData)
                ->through(config('indexers.signup', []))
                ->thenReturn();

            API::put('relations/signups/' . $data->id, $newData);
            API::put("elasticsearch/signup/{$newData->id}");

            if ($data->message ?? null) {
                return json_decode($data->message) ?? $data->message;
            }
        });
        return 0;
    }

}
