<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Models\Film;
use App\Models\Director;
use App\Models\Actor;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    // List of film directors and how many films they have in the poll
    $directorsWithFilmCount = DB::table('directors') 
    ->leftJoin('director_film', 'directors.id', '=', 'director_film.director_id') 
    ->leftJoin('films', 'director_film.film_id', '=', 'films.id') 
    ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid') 
    ->select(
        'directors.id', 
        'directors.name', 
        DB::raw('COUNT(DISTINCT films.id) as total_films'),  // Count distinct films
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  // Count critic votes
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes') // Count director votes
    )
    ->groupBy('directors.id', 'directors.name') 
    ->orderBy('total_films', 'desc') 
    ->get();

    

    // Group films by year to list how many films in each year
    $filmsByYear = DB::table('films')->groupBy('year')->select('year', DB::raw('count(films.id) as total_films'))
    ->orderBy('year', 'desc')
    ->get();

    // Group films by decade to list how many films in each decade
    $filmsByDecade = DB::table('films')
    ->select(DB::raw('FLOOR(year / 10) * 10 as decade'), DB::raw('count(id) as total_films'))
    ->groupBy('decade') // Group by the calculated decade
    ->orderBy('decade', 'desc') // Order by decade in descending order
    ->get();

    // Group films by country to list how many films per country
    $filmsByCountry = DB::table('countries') // Assuming you have a 'directors' table
        ->leftJoin('country_film', 'countries.id', '=', 'country_film.country_id') // Join with the pivot table
        ->leftJoin('films', 'country_film.film_id', '=', 'films.id') // Join with films table
        ->select('countries.name', DB::raw('count(films.id) as total_films')) // Select director's name and film count
        ->groupBy('countries.id', 'countries.name') // Group by director's id and name
        ->orderBy('total_films', 'desc') // Order by film count descending
        ->get();



        $totalCriticVotes = DB::table('votes')->where('type', 'critic')->count(); //d 4483 c 15247
        $totalDirectorVotes = DB::table('votes')->where('type', 'director')->count();
    
        $filmsByVote = DB::table('films')
        ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid')
        ->select(
            'films.id', 
            'films.name', 
            DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  // Count critic votes
            DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes'), // Count director votes
        )
        ->groupBy('films.id', 'films.name') // Group by film id and name
        ->orderBy('critic_votes', 'desc') // Order by the number of critic votes
        ->get();
    
    // dd($filmsByVote);

        
        return view('data', [
            'directorsWithFilmCount' => $directorsWithFilmCount,
            'filmsByYear' => $filmsByYear,
            'filmsByDecade' => $filmsByDecade,
            'filmsByCountry' => $filmsByCountry,
            'filmsByVote' => $filmsByVote,
            'totalCriticVotes' => $totalCriticVotes,
            'totalDirectorVotes' => $totalDirectorVotes
        ]);
});

// Route::get('/get_by_country/{country}', function ($country) {
//     $films = DB::table('films')
//     ->leftJoin('country_film', 'films.id', '=', 'country_film.film_id') // Join with the pivot table
//     ->join('countries', 'countries.id', '=', 'country_film.country_id') // Join with the countries table
//     ->join('poll_results', 'poll_results.film_id', 'films.id')
//     ->select('films.name', 'poll_results.rank') // Select all fields from films and the rank from the pivot table
//     ->where('countries.name', $country) // Filter by the specific country name
//     ->orderBy('poll_results.rank', 'asc') // Order by rank in ascending order
//     ->get();

//     return response()->json($films);
// });


Route::get('/directors', function () {
    $directorsWithFilmCountOrderByTotalFilms = DB::table('directors') // Assuming you have a 'directors' table
    ->leftJoin('director_film', 'directors.id', '=', 'director_film.director_id') // Join with the pivot table
    ->leftJoin('films', 'director_film.film_id', '=', 'films.id') // Join with films table
    ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid') // Join with votes table
    ->select(
        'directors.id', 
        'directors.name', 
        DB::raw('COUNT(DISTINCT films.id) as total_films'),  // Count distinct films
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  // Count critic votes
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes'),// Count director votes
        DB::raw('GROUP_CONCAT(DISTINCT films.name) as film_titles') // Concatenate film titles
    )
    ->groupBy('directors.id', 'directors.name') // Group by director's id and name
    ->orderBy('total_films', 'desc') // Order by film count descending
    ->get();

    $totalCriticVotes = DB::table('votes')->where('type', 'critic')->count(); //d 4483 c 15247
    $totalDirectorVotes = DB::table('votes')->where('type', 'director')->count();
    return view('directors', [
        'directorsWithFilmCountOrderByTotalFilms' => $directorsWithFilmCountOrderByTotalFilms,
        'totalCriticVotes' => $totalCriticVotes,
        'totalDirectorVotes' => $totalDirectorVotes
    ]);
});

Route::get('/actors', function () {
    $actorsWithFilmCount = DB::table('actors')
    ->leftJoin('actor_film', 'actors.id', '=', 'actor_film.actor_id') // Join with the pivot table
    ->leftJoin('films', 'actor_film.film_id', '=', 'films.id') // Join with films table
    ->leftJoin(DB::raw("(SELECT film_uuid, COUNT(CASE WHEN votes.type = 'critic' THEN 1 END) as critic_votes 
                         FROM votes 
                         GROUP BY film_uuid) as vote_counts"), 
               'films.uuid', '=', 'vote_counts.film_uuid') // Join with subquery to avoid duplicates
    ->select(
        'actors.id', 
        'actors.name', 
        DB::raw('COUNT(DISTINCT films.id) as total_films'), // Count distinct films for each actor
        DB::raw('GROUP_CONCAT(DISTINCT films.name) as film_titles'), // Group film titles (without SEPARATOR)
        DB::raw('SUM(critic_votes) as total_critic_votes') // Sum of critic votes from the subquery
    )
    ->groupBy('actors.id', 'actors.name') // Group by actor's id and name
    ->orderBy('total_films', 'desc') // Order by film count descending
    ->get();
    $totalCriticVotes = DB::table('votes')->where('type', 'critic')->count(); //d 4483 c 15247

    return view('actors', [
        'actorsWithFilmCount' => $actorsWithFilmCount,
        'totalCriticVotes' => $totalCriticVotes,
    ]);
});


Route::get('/display', function () {
    $filmsByVote = DB::table('films')
    ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid')
    ->join('country_film', 'films.id', '=', 'country_film.film_id') // Join with the pivot table
    ->join('countries', 'country_film.country_id', '=', 'countries.id') // Correct the join condition
    ->leftJoin('director_film', 'films.id', '=', 'director_film.film_id') // Join with the pivot table
    ->leftJoin('directors', 'directors.id', '=', 'director_film.director_id') // Join with directors
    ->select(
        'films.*', 
        'directors.name as director_name',  // Select director name
        'countries.name as country_name',     // Select country name
        DB::raw('FLOOR(films.year / 10) * 10 as decade'),
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  // Count critic votes
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes') // Count director votes
    )
    ->groupBy('films.id') // Group by film id, director name, and country name
    ->orderBy(DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END)'), 'desc') // Order by the number of critic votes
    ->limit(100)
    ->get();

    $topFilms = DB::table('films')
        ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid')
        ->leftJoin('director_film', 'films.id', '=', 'director_film.film_id')
        ->leftJoin('directors', 'directors.id', '=', 'director_film.director_id')
        ->select(
            'films.id',
            'films.name',
            DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes')
        )
        ->groupBy('films.id', 'films.name')
        ->orderBy('critic_votes', 'desc')
        ->limit(100)
        ->pluck('id'); // Get the ids of the top 100 films

    $countries = DB::table('country_film')
        ->join('countries', 'country_film.country_id', '=', 'countries.id')
        ->whereIn('country_film.film_id', $topFilms) // Filter by the top 100 films
        ->select('countries.name as country_name')
        ->groupBy('countries.id') // Group by country ID to avoid duplicates
        ->orderBy('countries.name', 'asc')
        ->get();
    // dd($countries);

    // dd($filmsByVote);
    return view('films', [
        'films' => $filmsByVote,
        'countries' => $countries
    ]);
});
