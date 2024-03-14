<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\SaunaController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\LogRouteActivity;

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
    ]);
});

// ログイン認証OKかつセッションOK
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [SaunaController::class, 'index'])->name('dashboard');
    // マイページ
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.mypage');
    
    // saunas 施設追加ページ表示(ログイン時のみ)
    Route::get('/saunas/create', [SaunaController::class, 'create'])->name('sauna.create');
    // saunas 施設追加処理(ログイン時のみ)
    Route::post('/saunas/create', [SaunaController::class, 'store'])->name('sauna.store');
    // saunas 施設編集表示(ログイン時のみ)
    Route::get('/saunas/edit/{id}', [SaunaController::class, 'edit'])->name('sauna.edit');
    // saunas 施設詳細更新処理(ログイン時のみ)
    Route::put('/saunas/edit/{id}', [SaunaController::class, 'update'])->name('sauna.update');
    Route::put('/saunas', [SaunaController::class, 'index'])->name('sauna.update');
    
    // saunas 一覧表示(検索画面)(未ログイン時でも表示)
    Route::get('/saunas', [SaunaController::class, 'index'])->name('sauna.index');
    // saunas 施設情報(未ログインでも表示)
    Route::get('/saunas/{id}', [SaunaController::class, 'show'])->name('sauna.show');
    
    // レビュー投稿(ログイン時のみ)
    Route::post('/saunas/{id}/review', [ReviewController::class, 'store'])->name('review.store');
});

// saunas 一覧表示(検索画面)(未ログイン時でも表示)
// Route::get('/saunas', [SaunaController::class, 'index'])->name('sauna.index');

// saunas 施設情報(未ログインでも表示)
// Route::get('/saunas/{id}', [SaunaController::class, 'show'])->name('sauna.show');

// saunas 施設情報削除処理
// Route::delete('/saunas/{id}', [SaunaController::class, 'destroy'])->name('sauna.destroy');


