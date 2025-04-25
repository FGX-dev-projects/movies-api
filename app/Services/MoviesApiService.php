<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoviesApiService
{
    protected $baseUrl;
    protected $username;
    protected $apiKey;

    public function __construct()
    {
        //get the values from env
        $this->baseUrl = env('MOVIES_API_BASE_URL');
        $this->username = env('MOVIES_API_USERNAME');
        $this->apiKey = env('MOVIES_API_KEY');
    }

    //get list of all cinemas
    public function getCinemas()
    {
        return Http::withBasicAuth($this->username, $this->apiKey)
            ->get("{$this->baseUrl}/getCinemas")
            ->json();
    }

    //get movies by cinema
    public function getMoviesByCinema($cinemaId)
    {
        return Http::withBasicAuth($this->username, $this->apiKey)
                ->get("{$this->baseUrl}/getMovieListMinimal", ['cinema_id' => $cinemaId])
                ->json();
    }

    

    //get movie poster image url
    public function getPosterUrl($movieId, $posterFileName, $resolution )
    {
        return config('services.movies_api.image_base') . "/{$movieId}/{$resolution}/{$posterFileName}";
    }
}