<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sauna;
use App\Models\Review;
use Illuminate\Http\Request;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

use Inertia\Inertia;

class UserController extends UserProfileController
{
    // ここに必要なカスタムロジックを追加します
    public function index()
    {
        // マイページに必要なユーザー情報
        $user = auth()->user();

        // ユーザーに基づいたレビューとサウナの施設名と都道府県(todo 直近の10個を表示)
        $reviews = $user->review()->with('sauna.prefecture')->get();
        // レビュー数
        $reviewsCount = $reviews->count();
        logger($reviews);
        // ユーザーに基づいたお気に入り施設
        $favorites = $user->favoriteSaunas()->get();
        // お気に入り数
        $favoritesCount = $favorites->count();
      logger($reviewsCount);
      logger($favoritesCount);

        // inertiaビューにユーザーのデータを渡して返す
        return inertia('Profile/Mypage', [
          'user' => $user,
          'reviews' => $reviews,
          'favorites' => $favorites,
          'reviewsCount' => $reviewsCount,
          'favoritesCount' => $favoritesCount,
        ]);
    }
}
