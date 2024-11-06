<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManualController;
use App\Http\Controllers\UserManualHistoryController;
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

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


// Guest Routes
Route::get('/user-manuals', [UserManualController::class, 'index']);
Route::get('/user-manuals/{id}', [UserManualController::class, 'show'])->where('id', '[0-9]+');
Route::get('/user-manuals/{id}/histories', [UserManualHistoryController::class, 'index']);

Route::get('/user-manuals/search', [UserManualController::class, 'userManualSearch']);

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Admin & Technical Writer Routes
    Route::post('/user-manuals', [UserManualController::class, 'store'])->middleware('abilities:user_manual:create');
    Route::put('/user-manuals/{id}', [UserManualController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/user-manuals/{id}', [UserManualController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/user-manuals/{id}/histories/{histories_id}', [UserManualHistoryController::class, 'destroy']);
    Route::get('/user-manuals/{id}/histories/{histories_id}', [UserManualHistoryController::class, 'show']);
        
    Route::get('/users', [AuthController::class, 'show']);
    Route::put('/users', [AuthController::class, 'update']);
    Route::delete('/logout', [AuthController::class, 'logout']);

    // Admin Routes
    Route::get('/admin/users', [UserController::class, 'index']);
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
    Route::put('/admin/users/{id}', [UserController::class, 'update']);

    Route::get('/admin/user-manual/trash', [UserManualController::class,'trash']);
    Route::get('/admin/user-manual/{id}/restore', [UserManualController::class, 'restoreUserManual']);
    Route::get('/admin/user-manual/{id}/delete', [UserManualController::class, 'deletePermanent']);

});

// Get current authenticated user
// Protecting Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});