<?php

use App\Http\Controllers\Admin\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DietController;
use App\Http\Controllers\User\UserDietController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
// Route::post('/createUser', function () {
//     // Token has both "check-status" and "place-orders" abilities...
// })->middleware('auth:sanctum');


// Admin
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::post('createUser', [UserController::class, 'createUser']);
    Route::get('getUsers', [UserController::class, 'getUsers']);
    Route::get('getUser', [UserController::class, 'getUser']);
    // Diet
    Route::post('addDiet', [DietController::class, 'addDiet']);
    Route::get('getUserDiets', [DietController::class, 'getUserDiets']);
    Route::put('deleteDiet', [DietController::class, 'deleteDiet']);
    // Blog
    Route::post('createBlog', [BlogController::class, 'createBlog']);
    Route::get('getMyBlogs', [BlogController::class, 'getMyBlogs']);
    Route::put('deleteBlog', [BlogController::class, 'deleteBlog']);
    // Recipes
    Route::post('createRecipes', [BlogController::class, 'createRecipes']);
    Route::get('getMyRecipes', [BlogController::class, 'getMyRecipes']);
    Route::put('deleteRecipes', [BlogController::class, 'deleteRecipes']);
    // SSS
    Route::post('createSSS', [BlogController::class, 'createRecipes']);
    Route::get('getMySSS', [BlogController::class, 'getMyRecipes']);
    Route::put('deleteSSS', [BlogController::class, 'deleteRecipes']);
});

// User
Route::middleware('auth:sanctum')->group(function () {
    Route::get('getMyDiets', [UserDietController::class, 'getMyDiets']);
    Route::get('getDietDetail', [UserDietController::class, 'getDietDetail']);
});
