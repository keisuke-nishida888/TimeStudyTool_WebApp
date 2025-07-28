<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/data', 'App\Http\Controllers\Api\ApiController@store');
Route::post('/get-mail-setting', [ApiController::class, 'getMailSetting']);
Route::get('/get-task-table', [ApiController::class, 'getTaskTable']);
Route::post('/api/time_study_import', [ApiController::class, 'timeStudyImport']);
