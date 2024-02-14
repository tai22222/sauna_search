<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

use Inertia\Inertia;

class UserController extends UserProfileController
{
    // ここに必要なカスタムロジックを追加します
    public function index()
    {
        // ユーザーの一覧とそれに関連する性別データを取得する
        // $users = User::with('gender')->get();

        // inertiaビューにユーザーのデータを渡して返す
        // return inertia('Users/Index', ['users' => $users]);
    }
}
