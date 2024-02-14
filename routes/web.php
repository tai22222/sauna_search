<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SaunaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

// saunas 一覧表示(検索画面)
Route::get('/saunas', [SaunaController::class, 'index'])->name('sauna.index');
// saunas 施設追加ページ表示
Route::get('/saunas/create', [SaunaController::class, 'create'])->name('sauna.create');
// saunas 施設追加処理
Route::post('/saunas/create', [SaunaController::class, 'store'])->name('sauna.store');

// saunas 施設情報
Route::get('/saunas/{id}', [SaunaController::class, 'show'])->name('sauna.show');
// saunas 施設編集表示
Route::get('/saunas/edit/{id}', [SaunaController::class, 'edit'])->name('sauna.edit');
// saunas 施設詳細更新処理
Route::put('/saunas/edit/{id}', [SaunaController::class, 'update'])->name('sauna.update');
// saunas 施設情報削除処理
// Route::delete('/saunas/{id}', [SaunaController::class, 'destroy'])->name('sauna.destroy');