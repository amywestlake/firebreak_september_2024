<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Film;
use App\Models\Director;
use App\Models\Actor;
use App\Models\PollResult;
use App\Models\Country;
use Illuminate\Support\Facades\File;

class ImportResultData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:results';

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
        // this data is exported from d20 api
        $json = File::get(storage_path('app/results.json'));
        $data = json_decode($json, true);

        // Loop through the data and save to the database
        foreach ($data as $item) {
            // Create or update the Film record
            $film = Film::updateOrCreate(
                ['name' => $item['film']['name']],
                [
                    'name' => $item['film']['name'],
                    'uuid' => $item['film']['uuid'],
                    'year' => $item['film']['year'],
                    'image_base' => $item['film']['image']['base'] ?? null,
                    'image_relative' => $item['film']['image']['relative'] ?? null,
                    'image_main' => $item['film']['image']['main'] ?? null,
                    'image_thumb' => $item['film']['image']['thumb'] ?? null,
                    'image_preload' => $item['film']['image']['preload'] ?? null,
                    'full_description' => $item['film']['fullDescription'] ?? null,
                    'producer' => $item['film']['credits']['producer'] ?? null,
                    'writer' => $item['film']['credits']['writer'] ?? null,
                    'url' => $item['film']['url'] ?? null,
                ]
            );

            // Handle directors
            if (!empty($item['film']['credits']['director'])) {
                $directorName = $item['film']['credits']['director'];
                $director = Director::firstOrCreate(['name' => $directorName]);
                $film->directors()->syncWithoutDetaching($director->id);
            }

            // Handle actors
            if (!empty($item['film']['credits']['featuring'])) {
                $actors = explode(',', $item['film']['credits']['featuring']);
                foreach ($actors as $actorName) {
                    $actor = Actor::firstOrCreate(['name' => trim($actorName)]);
                    $film->actors()->syncWithoutDetaching($actor->id);
                }
            }

            // Create a PollResult record
            PollResult::create([
                'film_id' => $film->id, 
                'rank' => $item['rank'],
                'tied' => $item['tied'],
                'category' => $item['category'] ?? 'critics',
            ]);

            // Handle countries
            if (!empty($item['film']['productionCountries'])) {
                $countries = explode(',', $item['film']['productionCountries']);
                foreach ($countries as $countryName) {
                    if($countryName === 'USA'){
                        $countryName === 'United States of America';
                    }
                    $country = Country::firstOrCreate(['name' => trim($countryName)]);
                    $film->countries()->syncWithoutDetaching($country->id);
                }
            }
        }

        $this->info('Data imported successfully.');
        return 0;
    }
}