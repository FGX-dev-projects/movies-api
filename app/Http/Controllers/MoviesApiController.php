<?php

namespace App\Http\Controllers;

use App\Services\MoviesApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MoviesApiController extends Controller
{
    protected $movies;

    public function __construct(MoviesApiService $movies)
    {
        $this->movies = $movies;
    }

    //return a list of movies
    public function cinemas()
    {
        $cinemas = $this->movies->getCinemas();

        // Only keep cinemas with these IDs
        $allowedIds = [44, 25, 9, 39, 40];

        // If data is wrapped in a "data" key
        $filtered = collect($cinemas['data'] ?? [])
            ->whereIn('cinema_id', $allowedIds)
            ->values(); // reset array keys

        return response()->json($filtered);
    }

    public function movies($cinemaId)
    {
        $movies = $this->movies->getMoviesByCinema($cinemaId);
    
        foreach ($movies['data'] ?? [] as &$movie) {
            if (!empty($movie['poster_file']) && !empty($movie['movie_id'])) {
                $movie['poster_url'] = $this->movies->getPosterUrl($movie['movie_id'], $movie['poster_file']);
            } else {
                $movie['poster_url'] = asset('images/default-poster.jpg'); // Fallback image
            }
        }
    
        return response()->json($movies);
    }
    

}
