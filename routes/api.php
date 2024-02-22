<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SaunaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// DBの画像データを初期画面で表示するために取得
Route::get('/sauna/{id}', [SaunaController::class, 'getImage']);
// updateアクションでデータの更新
Route::put('/sauna/{id}', [SaunaController::class, 'update']);
// updateアクションでデータの追加、更新(画像データ)
Route::post('/sauna/{id}', [SaunaController::class, 'updateImage']);