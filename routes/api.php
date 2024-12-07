<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MatchmakingController;
use App\Http\Controllers\ConferenceController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('users', [AuthController::class, 'user'])->middleware('auth:sanctum');

Route::apiResource('conferences', ConferenceController::class)->middleware('auth:sanctum');

Route::post('companys', [CompanyController::class, 'store']);
Route::get('companys/{company}', [CompanyController::class, 'show'])->middleware('auth:sanctum');
Route::get('companys', [CompanyController::class, 'index'])->middleware('auth:sanctum');
Route::put('companys/{company}', [CompanyController::class, 'update'])->middleware('auth:sanctum');
Route::delete('companys/{company}', [CompanyController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('companys-by-conference', [CompanyController::class, 'getCompanyByConference'])->middleware('auth:sanctum');

Route::get('tables', [TableController::class, 'index'])->middleware('auth:sanctum');

Route::post('matchmakings', [MatchmakingController::class, 'store'])->middleware('auth:sanctum');
Route::post('matchmaking-approval/{matchmaking_id}', [MatchmakingController::class, 'matchMakingApproval'])->middleware('auth:sanctum');

Route::get('matchmakings/bycompany-book', [MatchmakingController::class, 'getMatchmakingByCompanyBook'])->middleware('auth:sanctum');
Route::get('matchmakings/bycompany-match', [MatchmakingController::class, 'getMatchmakingByCompanyMatch'])->middleware('auth:sanctum');
Route::get('matchmakings/approved-company', [MatchmakingController::class, 'getApprovedMatchmakingByCompany'])->middleware('auth:sanctum');
Route::get('matchmakings/company-calendar/{company_id}', [MatchmakingController::class, 'getMatchmakingByCompanyCalendar'])->middleware('auth:sanctum');

Route::get('dashboard-meja', [MatchmakingController::class, 'dashboardMeja'])->middleware('auth:sanctum');