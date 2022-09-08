<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Users
    Route::resource('users', UserController::class)->except('edit', 'create');
    Route::put('/users/activation/{user}', [UserController::class, 'activation']);

    // Roles and Permissions
    Route::resource('roles', RoleController::class)->except('edit');
    Route::get('/permissions', [RoleController::class, 'permissions']);

    // Auth User
    Route::get('/user', [AuthenticatedSessionController::class, 'authenticated']);

});
