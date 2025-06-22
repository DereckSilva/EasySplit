<?php

use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
});


/* Register */
Route::post('/register', [UserController::class, 'create']);
  
/* Login */
Route::get('/login', [LoginController::class, 'auth']);

//Route::middleware('auth_token')->group(function () {
  /* Expense */
  Route::post('/expenses', [ExpenseController::class, 'create']);
  Route::get('/expenses', [ExpenseController::class, 'index']);
  Route::get('/expense/{id}', [ExpenseController::class, 'show']);
  Route::patch('/expense', [ExpenseController::class, 'update']);
  Route::delete('/expense/{id}', [ExpenseController::class, 'remove']);
  
  /* Notifications */
  Route::patch('/notification/{id}', [NotificationController::class, 'read']);
  
  /* Register */
  Route::patch('/register/new-password', [UserController::class, 'updatePassword']);
  
  /* User */
  Route::get('/user/{id}', [UserController::class, 'show']);

//});
