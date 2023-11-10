<?php

namespace Netflex\Toolbox\Commands\Index;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Netflex\API\Facades\API;

class ForceIndexSignups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:signup:index {id}';

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

        $allEntries = API::get('relations/signups/entry/' . $this->argument('id'));

        $allSignups = [];

        $this->withProgressBar($allEntries, function ($data) {

            $newData = clone $data;


            $newData = app(Pipeline::class)
                ->send($newData)
                ->through(config('indexers.signup', []))
                ->thenReturn();

            $data = API::put("elasticsearch/signup/{$newData->id}");

            dump($data);
            if ($data->message ?? null) {
                return json_decode($data->message) ?? $data->message;
            }
        });
        return 0;
    }
}
