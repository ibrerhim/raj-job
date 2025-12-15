<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExternalApiController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (require authentication)
Route::middleware('auth:api')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // External API route
    Route::get('/external/users', [ExternalApiController::class, 'getUsers']);

    // User CRUD routes with permission middleware
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:users-list');
        
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users-create');
        
        Route::get('/{id}', [UserController::class, 'show'])
            ->middleware('permission:users-read');
        
        Route::put('/{id}', [UserController::class, 'update'])
            ->middleware('permission:users-update');
        
        Route::delete('/{id}', [UserController::class, 'destroy'])
            ->middleware('permission:users-delete');
        
        Route::post('/{id}/assign-roles', [UserController::class, 'assignRoles'])
            ->middleware('permission:users-assign-roles');
    });

    // Role routes (admin only)
    Route::prefix('roles')->middleware('role:admin')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions']);
    });

    // Permission routes (admin only)
    Route::prefix('permissions')->middleware('role:admin')->group(function () {
        Route::get('/', [PermissionController::class, 'index']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::get('/{id}', [PermissionController::class, 'show']);
        Route::put('/{id}', [PermissionController::class, 'update']);
        Route::delete('/{id}', [PermissionController::class, 'destroy']);
    });
});
