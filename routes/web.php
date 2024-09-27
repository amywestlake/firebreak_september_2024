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
        DB::raw('COUNT(DISTINCT films.id) as total_films'),  
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes') 
    )
    ->groupBy('directors.id', 'directors.name') 
    ->orderBy('total_films', 'desc') 
    ->get();

    // Group films by year to list how many films in each year
    $filmsByYear = DB::table('films')->groupBy('year')->select('year', DB::raw('count(films.id) as total_films'))
    ->orderBy('total_films', 'desc')
    ->limit(10)
    ->get();

    // Group films by decade to list how many films in each decade
    $filmsByDecade = DB::table('films')
    ->select(DB::raw('FLOOR(year / 10) * 10 as decade'), DB::raw('count(id) as total_films'))
    ->groupBy('decade') 
    ->orderBy('decade', 'desc') 
    ->get();

    // Group films by country to list how many films per country
    $filmsByCountry = DB::table('countries') 
        ->leftJoin('country_film', 'countries.id', '=', 'country_film.country_id') 
        ->leftJoin('films', 'country_film.film_id', '=', 'films.id') 
        ->select('countries.name', DB::raw('count(films.id) as total_films'))
        ->groupBy('countries.id', 'countries.name') 
        ->orderBy('total_films', 'desc') 
        ->get();

        $totalCriticVotes = DB::table('votes')->where('type', 'critic')->count(); //d 4483 c 15247
        $totalDirectorVotes = DB::table('votes')->where('type', 'director')->count();
    
        $filmsByVote = DB::table('films')
        ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid')
        ->select(
            'films.id', 
            'films.name', 
            DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  
            DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes'), 
        )
        ->groupBy('films.id', 'films.name') 
        ->orderBy('critic_votes', 'desc') 
        ->get();
           
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

Route::get('/directors', function () {
    $directorsWithFilmCountOrderByTotalFilms = DB::table('directors') 
    ->leftJoin('director_film', 'directors.id', '=', 'director_film.director_id') 
    ->leftJoin('films', 'director_film.film_id', '=', 'films.id') 
    ->leftJoin('votes', 'votes.film_uuid', '=', 'films.uuid') 
    ->select(
        'directors.id', 
        'directors.name', 
        DB::raw('COUNT(DISTINCT films.id) as total_films'), 
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'), 
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes'),
        DB::raw('GROUP_CONCAT(DISTINCT films.name) as film_titles') 
    )
    ->groupBy('directors.id', 'directors.name')
    ->orderBy('total_films', 'desc') 
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
    ->leftJoin('actor_film', 'actors.id', '=', 'actor_film.actor_id')
    ->leftJoin('films', 'actor_film.film_id', '=', 'films.id')
    ->leftJoin(DB::raw("(SELECT film_uuid, COUNT(CASE WHEN votes.type = 'critic' THEN 1 END) as critic_votes 
                         FROM votes 
                         GROUP BY film_uuid) as vote_counts"), 
               'films.uuid', '=', 'vote_counts.film_uuid') 
    ->select(
        'actors.id', 
        'actors.name', 
        DB::raw('COUNT(DISTINCT films.id) as total_films'), 
        DB::raw('GROUP_CONCAT(DISTINCT films.name) as film_titles'), 
        DB::raw('SUM(critic_votes) as total_critic_votes')
    )
    ->groupBy('actors.id', 'actors.name') 
    ->orderBy('total_films', 'desc') 
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
    ->join('country_film', 'films.id', '=', 'country_film.film_id') 
    ->join('countries', 'country_film.country_id', '=', 'countries.id') 
    ->leftJoin('director_film', 'films.id', '=', 'director_film.film_id')
    ->leftJoin('directors', 'directors.id', '=', 'director_film.director_id') 
    ->select(
        'films.*', 
        'directors.name as director_name',  
        'countries.name as country_name',  
        DB::raw('FLOOR(films.year / 10) * 10 as decade'),
        DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END) as critic_votes'),  
        DB::raw('COUNT(CASE WHEN votes.type = "director" THEN 1 END) as director_votes') 
    )
    ->groupBy('films.id') 
    ->orderBy(DB::raw('COUNT(CASE WHEN votes.type = "critic" THEN 1 END)'), 'desc') 
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
        ->whereIn('country_film.film_id', $topFilms) 
        ->select('countries.name as country_name')
        ->groupBy('countries.id') 
        ->orderBy('countries.name', 'asc')
        ->get();

    return view('films', [
        'films' => $filmsByVote,
        'countries' => $countries
    ]);
});
