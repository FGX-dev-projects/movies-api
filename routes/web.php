<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesApiController;

Route::get('/cinemas', [MoviesApiController::class, 'cinemas']);
Route::get('/movies/{cinemaId}', [MoviesApiController::class, 'movies']);
Route::get('/movie/{movieId}', [MoviesApiController::class, 'movieDetails']);
// Route::post('/invalidate-cache', [MoviesApiController::class, 'invalidateCache']);