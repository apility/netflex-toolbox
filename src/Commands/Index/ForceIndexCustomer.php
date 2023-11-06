<?php

namespace Netflex\Toolbox\Commands\Index;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Netflex\API\Facades\API;
use Netflex\Toolbox\Traits\IndexHelpers;

class ForceIndexCustomer extends Command
{
    use IndexHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tb:customers:index {id?} {{--dry-run}}';

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

        do {

            /// Fetch all customer ids that are in the database(this endpoint should not use elastic search)
            $this->info('Fetching all IDs from database');
            $allCustomers = collect(API::get('relations/customers'))->pluck('id')->map(fn($id) => intval($id))->values();
            $this->info("Found {$allCustomers->count()} customers...");

            $existing = collect();

            $id = 0;


            if ($arg = $this->argument('id')) {
                $this->info("Indexing customer id: $arg");
                $this->dumpIf($this->indexCustomer($arg));
                $notIndexed = collect();
            } else {

                do {

                    $query = User::limit(100000)
                        ->fields(['id'])
                        ->where('id', '>', $id)
                        ->orderBy('id', 'asc')
                        ->get()
                        ->pluck('id')
                        ->map(fn($id) => intval($id))
                        ->values();
                    $existing = $existing->merge($query);
                    $id = $existing->max();
                } while ($query->count() > 0);

                $notIndexed = $allCustomers->reject(fn(int $id) => $existing->contains($id))->shuffle();
                $this->info("\n", $notIndexed->count() . " remaining");
                $this->info("For example: " . $notIndexed->slice(0, 10)->join(', '));

                $this->withProgressBar($notIndexed->slice(0, 100), function ($id) {
                    $this->info($id);
                    $this->dumpIf($this->indexCustomer($id));
                });
            }
        } while ($notIndexed->count() > 0);
        return 0;
    }

    private function indexCustomer($id)
    {
        $customerData = API::get('relations/customers/customer/' . $id);

        $newData = clone $customerData;

        $newData = app(Pipeline::class)
            ->send($newData)
            ->through(config('indexers.customer', []))
            ->thenReturn();

        $this->removeUnchangedFields($newData, $customerData);

        if ($this->option('dry-run')) {
            dump($newData);
        } else {
            if (sizeof(array_keys((array)$newData)) > 0)
                API::put("relations/customers/customer/$id", $newData);
        }

        $data = API::put("elasticsearch/customer/$id");

        if ($data->message ?? null) {
            return json_decode($data->message) ?? $data->message;
        }
    }




}
