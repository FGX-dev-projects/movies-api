<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesApiController;

Route::get('/cinemas', [MoviesApiController::class, 'cinemas']);
Route::get('/api/stats', [MoviesApiController::class, 'apiStats']);
//Route::get('/api/debug', [MoviesApiController::class, 'debugApiResponse']);
Route::get('/api/cache-inspection', [MoviesApiController::class, 'cacheInspection']);
Route::get('/api/frequency-proof', [MoviesApiController::class, 'frequencyProof']);
Route::get('/movies/{cinemaId}', [MoviesApiController::class, 'movies']);
// Route::get('/movie/{movieId}', [MoviesApiController::class, 'movieDetails']); // Commented out as movieDetails() is not defined
// Route::post('/invalidate-cache', [MoviesApiController::class, 'invalidateCache']); // Uncomment for testing if needed