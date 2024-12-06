<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConferenceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('conferences', ConferenceController::class)->middleware('auth:sanctum');

Route::post('companys', [CompanyController::class, 'store']);
Route::get('companys/{id}', [CompanyController::class, 'show'])->middleware('auth:sanctum');
Route::get('companys', [CompanyController::class, 'index'])->middleware('auth:sanctum');
Route::put('companys/{id}', [CompanyController::class, 'update'])->middleware('auth:sanctum');
Route::delete('companys/{id}', [CompanyController::class, 'destroy'])->middleware('auth:sanctum');