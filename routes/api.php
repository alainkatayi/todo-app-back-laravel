<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\taskController;
use App\Http\Controllers\UserController;

//route pour recuperer le user conneer
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//route pour la recuperatiion de la liste des users
Route::get('list_user', [UserController::class, 'list']) -> middleware('auth:sanctum');
//route pour la creation du user
Route::post('/register', [authController::class, 'register']);
//route pour la creation du user
Route::post('/login', [authController::class, 'login'])->name('login');
//route pour la deconnexion du user
Route::post('/logout', [authController::class, 'logout']) ->middleware('auth:sanctum');
//route pour le task(create, delete, update etc) proteger
Route::apiResource('/tasks', taskController::class )->middleware('auth:sanctum');