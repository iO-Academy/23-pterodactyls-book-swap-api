<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(BookController::class)->group(function () {
    Route::get('/books', 'getAllBooks');
    Route::get('/books/{id}', 'getBookFromId');
    Route::put('/books/claim/{id}', 'claimBook');
    Route::put('/books/return/{id}', 'returnBook');
    Route::post('/books/', 'addBook');
});

Route::post('/reviews', [ReviewController::class, 'addReview']);
