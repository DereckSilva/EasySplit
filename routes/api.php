<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\CheckCountRequest;
use Illuminate\Support\Facades\Route;

/* Status API */
Route::get('/status', function (){
  return response()->json(['message' => 'api funcionando corretamente']);
});

/* Register */
Route::post('/register', [UserController::class, 'create']);

/* Login */
Route::post('/login', [LoginController::class, 'auth']);

Route::middleware(['auth:sanctum'])->group(function () {

    /* Logout */
    Route::post('/logout', [LoginController::class, 'logout']);

    /* Expense */
    Route::prefix('expense')->group(function () {
        Route::post('', [ExpenseController::class, 'create']);
        Route::get('/owner', [ExpenseController::class, 'allOwner']);
        Route::get('/intermediary', [ExpenseController::class, 'allIntermediary']);
        Route::get('/{id}', [ExpenseController::class, 'show']);
        Route::put('', [ExpenseController::class, 'update']);
        Route::delete('/{id}', [ExpenseController::class, 'remove']);
        Route::put('/notification', [ExpenseController::class, 'expenseNotification']);
        Route::post('/import', [ExpenseController::class, 'importExpenseFromCSV']);
    });

    /* Notifications */
    Route::patch('/notification/{id}', [NotificationController::class, 'read']);

    /* ChatBot */
    Route::post('/chatBot', [GeminiController::class, 'chat'])->middleware(CheckCountRequest::class);

    /* User */
    Route::prefix('user')->group(function () {
        Route::get('{id}', [UserController::class, 'show']);
        Route::patch('/updated', [UserController::class, 'updated']);
        Route::patch('/new-password', [UserController::class, 'updatePassword']);
        Route::delete('/delete', [UserController::class, 'delete']);
    });
});
