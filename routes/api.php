<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/api/users', [UserController::class, 'index']);
Route::post('/api/users', [UserController::class, 'store']);
Route::delete('/api/users/{id}', [UserController::class, 'destroy']);
Route::patch('/api/users/{id}/increment', [UserController::class, 'increment']);
Route::patch('/api/users/{id}/decrement', [UserController::class, 'decrement']);
Route::get('/api/users/{id}', [UserController::class, 'show']);
Route::get('/api/grouped-by-score', [UserController::class, 'groupedByScore']);
