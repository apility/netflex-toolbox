<?php

namespace Netflex\Toolbox\Commands\Index;

use Illuminate\Console\Command;
use Netflex\API\Facades\API;

class ForceIndexNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:newsletter:index {ids}';

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

        $newsletters = explode(",", $this->argument('ids'));
        $this->withProgressBar($newsletters, function ($id) {
            $value = API::put('elasticsearch/newsletter/' . $id);

            if ($value->message ?? null) {
                dump($value->message);
            }
        });
        return 0;
    }
}
