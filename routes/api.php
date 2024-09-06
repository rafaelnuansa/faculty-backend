<?php

use Illuminate\Support\Facades\Route;

// route login
Route::post('/login', [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

// group route with middleware "auth"
Route::group(['middleware' => 'auth:api'], function() {

    // logout
    Route::post('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
});

// group route with prefix "admin"
Route::prefix('admin')->group(function () {
    // group route with middleware "auth:api"
    Route::group(['middleware' => 'auth:api'], function () {

        // dashboard
        Route::get('/dashboard', App\Http\Controllers\Api\Admin\DashboardController::class);

        // users routes (replace apiResource)
        Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
        Route::post('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'store']);
        Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'show']);
        Route::put('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'update']);
        Route::delete('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);

        // fetch all faculties (specific route)
        Route::get('/faculties/all', [App\Http\Controllers\Api\Admin\FacultyController::class, 'all']);

        // faculties routes (replace apiResource)
        Route::get('/faculties', [App\Http\Controllers\Api\Admin\FacultyController::class, 'index']);
        Route::post('/faculties', [App\Http\Controllers\Api\Admin\FacultyController::class, 'store']);
        Route::get('/faculties/{faculty}', [App\Http\Controllers\Api\Admin\FacultyController::class, 'show']);
        Route::put('/faculties/{faculty}', [App\Http\Controllers\Api\Admin\FacultyController::class, 'update']);
        Route::delete('/faculties/{faculty}', [App\Http\Controllers\Api\Admin\FacultyController::class, 'destroy']);


        // categories routes (replace apiResource)
        Route::get('/categories', [App\Http\Controllers\Api\Admin\CategoryController::class, 'index']);
        Route::post('/categories', [App\Http\Controllers\Api\Admin\CategoryController::class, 'store']);
        Route::get('/categories/{category}', [App\Http\Controllers\Api\Admin\CategoryController::class, 'show']);
        Route::put('/categories/{category}', [App\Http\Controllers\Api\Admin\CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [App\Http\Controllers\Api\Admin\CategoryController::class, 'destroy']);


        // posts routes (replace apiResource)
        Route::get('/posts', [App\Http\Controllers\Api\Admin\PostController::class, 'index']);
        Route::post('/posts', [App\Http\Controllers\Api\Admin\PostController::class, 'store']);
        Route::get('/posts/{post}', [App\Http\Controllers\Api\Admin\PostController::class, 'show']);
        Route::put('/posts/{post}', [App\Http\Controllers\Api\Admin\PostController::class, 'update']);
        Route::delete('/posts/{post}', [App\Http\Controllers\Api\Admin\PostController::class, 'destroy']);
  });
});
