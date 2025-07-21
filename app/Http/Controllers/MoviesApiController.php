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
        $cinemas = Cache::remember('cinemas_list', now()->addDays(30), function () {
            $response = $this->movies->getCinemas();
            return is_array($response) && isset($response['data']) ? $response : ['data' => []];
        });

        //$allowedIds = [44, 25, 9, 39, 40];
        //$allowedIds = [9, 11, 12, 13];
        $allowedIds = [11];

        $filtered = collect($cinemas['data'])
            ->whereIn('cinema_id', $allowedIds)
            ->values();

        return response()->json($filtered);
    }

    // Return movies for a specific cinema (cache until next Monday)
    public function movies($cinemaId)
    {
        $cacheKey = 'movies_cinema_' . $cinemaId;

        $movies = Cache::remember($cacheKey, now()->next('Monday'), function () use ($cinemaId) {
            $response = $this->movies->getMoviesByCinema($cinemaId);
            return is_array($response) && isset($response['data']) ? $response : ['data' => []];
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

    // Manual cache invalidation endpoint (protected by API key)
    // public function invalidateCache(Request $request)
    // {
    //     // Validate API key (store in .env, e.g., env('CACHE_INVALIDATION_KEY'))
    //     if ($request->header('X-API-Key') !== env('CACHE_INVALIDATION_KEY')) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     // Clear cinema cache
    //     Cache::forget('cinemas_list');

    //     // Clear movie caches for all cinema IDs
    //     //$allowedIds = [44, 25, 9, 39, 40];
    //     $allowedIds = [9, 11, 12, 13];
    //     foreach ($allowedIds as $id) {
    //         Cache::forget('movies_cinema_' . $id);
    //     }

    //     return response()->json(['message' => 'Cache invalidated successfully']);
    // }
}