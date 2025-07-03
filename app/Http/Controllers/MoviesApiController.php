<?php

namespace App\Http\Controllers;

use App\Services\MoviesApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MoviesApiController extends Controller
{
    protected $movies;

    public function __construct(MoviesApiService $movies)
    {
        $this->movies = $movies;
    }

    // Return a filtered list of cinemas (cache for 7 days)
    public function cinemas()
    {
        $cinemas = Cache::remember('cinemas_list', now()->addDays(7), function () {
            return $this->movies->getCinemas();
        });

        $allowedIds = [44, 25, 9, 39, 40];

        $filtered = collect($cinemas['data'] ?? [])
            ->whereIn('cinema_id', $allowedIds)
            ->values();

        return response()->json($filtered);
    }

    // Return movies for a specific cinema (cache for 6 days)
    public function movies($cinemaId)
    {
        $cacheKey = 'movies_cinema_' . $cinemaId;

        $movies = Cache::remember($cacheKey, now()->addDays(6), function () use ($cinemaId) {
            return $this->movies->getMoviesByCinema($cinemaId);
        });

        if (isset($movies['data']) && is_array($movies['data'])) {
            foreach ($movies['data'] as $key => $movie) {
                if (!empty($movie['movie_poster']) && !empty($movie['movie_id'])) {
                    $posterUrl = $this->movies->getPosterUrl(
                        $movie['movie_id'],
                        $movie['movie_poster'],
                        216
                    );
                    $movies['data'][$key]['poster_url'] = $posterUrl;
                } else {
                    $movies['data'][$key]['poster_url'] = asset('images/default-poster.jpg');
                }
            }
        }

        return response()->json($movies);
    }
}
