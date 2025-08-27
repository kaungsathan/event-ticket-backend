<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Domains\Users\Controllers\RoleController;
use App\Domains\Users\Controllers\UserController;

Route::middleware('auth:sanctum')->group(function () {

    // User profile routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Role management (admin only)
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'permissions']);
        Route::post('/roles/{role}/permissions/assign', [RoleController::class, 'assignPermissions']);
        Route::post('/roles/{role}/permissions/revoke', [RoleController::class, 'revokePermissions']);
    });

    // Admin dashboard
    Route::middleware(['role:super-admin,admin,event-manager,customer-service'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => 'Welcome to the admin dashboard!',
                    'user_role' => auth()->user()->getPrimaryRole(),
                    'permissions' => auth()->user()->getPermissionNames(),
                ]
            ]);
        });
    });

    Route::group(['prefix' => 'users', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view-users');
        Route::get('/{id}', [UserController::class, 'show'])->middleware('permission:view-users');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create-users');
        Route::post('/{id}/update', [UserController::class, 'update'])->middleware('permission:edit-users');
        Route::delete('/{id}', [UserController::class, 'destroy'])->middleware('permission:delete-users');
    });
});
