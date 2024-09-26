<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vote;
use Illuminate\Support\Facades\File;

class ImportVotesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:votes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from a JSON file into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Load JSON data from file
        // this data comes from the polls-api service
        $json = File::get(storage_path('app/votes.json'));
        $data = json_decode($json, true);

        // Loop through the data and save to the database
        foreach ($data as $item) {
            // Create or update the Film record
            $vote = Vote::updateOrCreate(
                ['id' => $item['id']],
                [
                    'voter_id' => $item['voter_id'],
                    'film_uuid' => $item['uuid'],
                    'rank' => $item['order'],
                    'type' => $item['type'],
                ]
            );

        }

        $this->info('Data imported successfully.');
        return 0;
    }
}