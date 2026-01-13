<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/* Status API */
Route::get('/status', function (){
  return response()->json(['message' => 'api funcionando corretamente']);
});

/* Register */
Route::post('/register', [UserController::class, 'create']);

/* Login */
Route::post('/login', [LoginController::class, 'auth']);

Route::post('/import-expense', [ExpenseController::class, 'importExpenseFromCSV']);

Route::middleware('auth:sanctum')->group(function () {

    /* Expense */
    Route::post('/expenses', [ExpenseController::class, 'create']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expense/{id}', [ExpenseController::class, 'show']);
    Route::patch('/expense', [ExpenseController::class, 'update']);
    Route::delete('/expense/{id}', [ExpenseController::class, 'remove']);
    Route::patch('/expense-notification', [ExpenseController::class, 'expenseNotification']);

    /* Notifications */
    Route::patch('/notification/{id}', [NotificationController::class, 'read']);

    /* Register */
    Route::patch('/register/new-password', [UserController::class, 'updatePassword']);

    /* User */
    Route::prefix('user')->group(function () {
        Route::get('{id}', [UserController::class, 'show']);
        Route::patch('/updated', [UserController::class, 'updated']);
    });
});
