<?php

namespace App\Http\Controllers;

use App\Models\Review;

// saunasテーブルに外部キーで設定したテーブルのモデルの読み込み
use App\Models\FacilityType;

// フォームのバリデーション(ミドルウェア)
use App\Http\Requests\SaunaRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
  // 一覧表示
  public function index(Request $request){
    
  }


  // 新規作成表示
  public function create(){
  }

  // 新規作成処理
  public function store(SaunaRequest $request){
    $data = $request->all();
    logger($data);

    // 画像保存
    if ($request->hasFile('review_image')) {
      logger('画像ファイルがあった場合');
      $mainImagePath = $request->file('review_image')->store('review-images', 'public');
      $data['review_image'] = $mainImagePath;
      logger($data);
    }
    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
      Review::create($data);

      DB::commit();
      // 成功した場合の処理
      logger('DBの挿入が成功しました');
      return redirect('/saunas/')->with('flash.successMessage', 'サ活を投稿しました。');

    } catch (\Exception $e) {
        DB::rollBack();
        // エラーハンドリング
        logger('DBの挿入が失敗しました');
        logger($e->getMessage());
        return back()->withInput()->with('flash', [
          'errorMessage' => '登録中にエラーが発生しました。'
        ]);
    }
  }

  // 詳細表示
  public function show($id){
  }

  // 詳細編集画面表示
  public function edit($id){

  }

  // 更新処理
  public function update(Request $request, $id){
  }

  // 削除
  public function destroy($id){
    return redirect()->route('saunas.index');
  }
}