<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route::get('/posts', [PostController::class, 'index']); // Без middleware
// Route::post('/add_post', [PostController::class, 'store']); // Без middleware
// Route::put('/posts/{post}', [PostController::class, 'update']); // Без middleware
// Route::delete('/posts/{post}', [PostController::class, 'destroy']);

Route::get('/show_posts', [PostController::class, 'index'])->middleware('jwt.auth');
Route::post('/add_post', [PostController::class, 'store'])->middleware('jwt.auth');
Route::put('/update_post/{post}', [PostController::class, 'update'])->middleware('jwt.auth');
Route::delete('/delete_post/{post}', [PostController::class, 'destroy'])->middleware('jwt.auth');