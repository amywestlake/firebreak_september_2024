<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voter;
use Illuminate\Support\Facades\File;

class ImportVoterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:voters';

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
        $json = File::get(storage_path('app/voters.json'));
        $data = json_decode($json, true);

        // Loop through the data and save to the database
        foreach ($data as $item) {
            // Create or update the voter record
            $voter = Voter::updateOrCreate(
                ['id' => $item['id']],
                [
                    'firstname' => $item['firstname'],
                    'surname' => $item['surname'],
                    'sortName' => $item['sort_name'],
                    'type' => $item['type'],
                    'country' => $item['country'],
                ]
            );

        }

        $this->info('Data imported successfully.');
        return 0;
    }
}