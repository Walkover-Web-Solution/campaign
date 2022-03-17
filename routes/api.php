<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CampaignsController;
use App\Http\Controllers\API\ChannelTypesController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\CompanyTokenIPsController;
use App\Http\Controllers\API\IPTypesController;
use App\Http\Controllers\API\TokensController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\API\TemplatesController;
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

Route::get('/testing', [TestingController::class, 'index'])->withoutMiddleware('authby.jwt');

Route::post('/register', [AuthController::class, 'register']);

Route::resource('/campaigns', CampaignsController::class);

Route::resource('/tokens/{token}/ips', CompanyTokenIPsController::class);

Route::resource('/tokens', TokensController::class);

Route::get('/iptypes', [IPTypesController::class, 'index']);

Route::get('/channeltypes', [ChannelTypesController::class, 'index']);

Route::get('/clients', [ClientController::class, 'index'])->withoutMiddleware('authby.jwt');

Route::resource('/templates', TemplatesController::class);

Route::post('/encode', [TestingController::class, 'encodeData'])->withoutMiddleware('authby.jwt');
