<?php

use App\Http\Controllers\API\ActionLogsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CampaignsController;
use App\Http\Controllers\API\ChannelTypesController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\CompanyTokenIPsController;
use App\Http\Controllers\API\IPTypesController;
use App\Http\Controllers\API\TokensController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\API\FlowActionsController;
use App\Http\Controllers\API\CampaignLogController;
use App\Http\Controllers\API\ConditionsController;
use App\Http\Controllers\API\RunCampaignController;
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

Route::post('/tokens/{token}/associate', [TokensController::class, 'associate']);

Route::get('/iptypes', [IPTypesController::class, 'index']);

Route::get('/channeltypes', [ChannelTypesController::class, 'index']);

Route::get('/clients', [ClientController::class, 'index'])->withoutMiddleware('authby.jwt');

Route::post('/campaigns/{slug}/run', [RunCampaignController::class, 'run'])->withoutMiddleware('authby.jwt')->middleware('authby.jwt.token');

Route::post('/campaigns/{slug}/dryrun', [RunCampaignController::class, 'dryRun']);

Route::post('/encode', [TestingController::class, 'encodeData'])->withoutMiddleware('authby.jwt');

Route::get('/conditions', [ConditionsController::class, 'index']);

Route::get('/campaigns/{slug}/fields', [CampaignsController::class, 'getFields']);

Route::get('/campaigns/{slug}/snippets', [CampaignsController::class, 'getSnippets']);

Route::post('/campaigns/{slug}/copy', [CampaignsController::class, 'copy']);

Route::get('/logs', [ActionLogsController::class, 'index']);

Route::resource('/{slug}/flowActions', FlowActionsController::class);

Route::resource('/{slug}/campaignLogs', CampaignLogController::class);
