<?php

namespace App\Http\Controllers;

use App\Models\Sauna;

// saunasテーブルに外部キーで設定したテーブルのモデルの読み込み
use App\Models\FacilityType;
use App\Models\UsageType;
use App\Models\Prefecture;

// 各テーブルでsauna_idを持つDBの取得のため
use App\Models\SaunaInfo;
use App\Models\SaunaType;
use App\Models\StoveType;
use App\Models\HeatType;

use App\Models\WaterBath;
use App\Models\WaterType;
use App\Models\BathType;

// フォームのバリデーション(ミドルウェア)
use App\Http\Requests\SaunaRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaunaController extends Controller
{
  // 一覧表示
  public function index(){
    $sessionData = session()->all();
    return Inertia::render('Sauna/Index', ['sessionData' => $sessionData]);
  }


  // 新規作成表示
  public function create(){
    // saunasテーブルと外部キー設定しているテーブル情報の取得
    $facilityTypes = FacilityType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $usageTypes = UsageType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $prefecture = Prefecture::select('id', 'name')->where('delete_flag', 0)->get();
    
    $saunas = Sauna::with('facilityType','usageType', 'prefecture')->get();

    // sauna_infosテーブルと外部キー設定しているテーブル情報の取得
    $saunaTypes = SaunaType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $stoveTypes = StoveType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $heatTypes = HeatType::select('id', 'source_name')->where('delete_flag', 0)->get();

    $saunaInfos = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->get();

    // water_bathsテーブルと外部キー設定しているテーブル情報の取得
    $waterTypes = WaterType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $bathTypes = BathType::select('id', 'type_name')->where('delete_flag', 0)->get();

    $waterBaths = WaterBath::with('bathType', 'waterType')->get();

    // images_facilitiesテーブルと外部キー設定しているテーブル情報の取得
    // business_hoursテーブルと外部キー設定しているテーブル情報の取得


    // Create.vueに渡すオブジェクト・配列
    return inertia('Sauna/Create', [
      'saunas' => $saunas,
      'facilityTypes' => $facilityTypes,
      'usageTypes' => $usageTypes,
      'prefectures' => $prefecture,
      'saunaInfos' => $saunaInfos,
      'saunaTypes' => $saunaTypes,
      'stoveTypes' => $stoveTypes,
      'heatTypes' => $heatTypes,
      'waterBaths' => $waterBaths,
      'waterTypes' => $waterTypes,
      'bathTypes' => $bathTypes,
    ]);
  }

  // 新規作成処理
  public function store(SaunaRequest $request){
    // バリデーション後のリクエストの受け取り(SaunaRequest)
    // Create.vueからフォームを受け取り、sauna_infosテーブル、water_bathsテーブル、business_hoursテーブル、images_facilitiesテーブルに振り分ける
    // リクエストからデータを取得し、カラム名を変更
    $saunaData = $request->only([
        'user_id','facility_name', 'facility_type_id', 'usage_type_id', 'prefecture_id',
        'address1', 'address2', 'address3', 'access_text', 'tel', 'website_url',
        'business_hours_detail', 'min_fee', 'fee_text'
    ]);

    $saunaInfoData = $request->only([
        'sauna_type_id', 'stove_type_id', 'heat_type_id', 'temperature_sauna',
        'capacity_sauna', 'additional_info_sauna'
    ]);

    $waterBathData = $request->only([
        'bath_type_id', 'water_type_id', 'temperature_water', 'capacity_water',
        'deep_water', 'additional_info_water'
    ]);

    // フォームで受け取ったカラム名をDBと合わせる
    $saunaInfoKeyMap = [
      'temperature_sauna' => 'temperature',
      'capacity_sauna' => 'capacity',
      'additional_info_sauna' => 'additional_info',
    ];
    
    foreach ($saunaInfoKeyMap as $oldKey => $newKey) {
      if (isset($saunaInfoData[$oldKey])) {
          $saunaInfoData[$newKey] = $saunaInfoData[$oldKey];
          unset($saunaInfoData[$oldKey]);
      }
    }
    
    $waterBathKeyMap = [
        'temperature_water' => 'temperature',
        'capacity_water' => 'capacity',
        'additional_info_water' => 'additional_info',
    ];
    
    foreach ($waterBathKeyMap as $oldKey => $newKey) {
      if (isset($waterBathData[$oldKey])) {
          $waterBathData[$newKey] = $waterBathData[$oldKey];
          unset($waterBathData[$oldKey]);
      }
    }

    // ログに配列を出力する
    logger($saunaData);
    logger('===========saunasテーブル挿入データ===========');
    logger($saunaInfoData);
    logger('===========sauna_infosテーブル挿入データ===========');
    logger($waterBathData);
    logger('===========water_bathsテーブル挿入データ===========');

    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
      // モデルを作成してデータを保存
      $sauna = Sauna::create($saunaData);
      $saunaInfo = SaunaInfo::create(array_merge($saunaInfoData, ['sauna_id' => $sauna->id]));
      $waterBath = WaterBath::create(array_merge($waterBathData, ['sauna_id' => $sauna->id]));

      DB::commit();
      // 成功した場合の処理
      logger('DBの挿入が成功しました');
      return redirect('/saunas')->with('success', '登録が成功しました。');

    } catch (\Exception $e) {
        DB::rollBack();
        // エラーハンドリング
        logger('DBの挿入が失敗しました');
        logger($e->getMessage());
        return back()->withInput()->with('error', '登録中にエラーが発生しました。');
    }
  }

  // 詳細表示
  public function show($id){
    $sauna = Sauna::findOrFail($id);

    return Inertia::render('Sauna/Info');
  }

  // 詳細編集画面表示
  public function edit($id){
        $sauna = Sauna::with('facilityType', 'usageType', 'prefecture')->findOrFail($id);
            
        // $saunaInfo = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->where('sauna_id', $sauna->id)->get();
        $saunaInfo = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->where('sauna_id', $sauna->id)->first();
        // $waterBath = WaterBath::with('bathType', 'waterType')->where('sauna_id', $sauna->id)->get();
        $waterBath = WaterBath::with('bathType', 'waterType')->where('sauna_id', $sauna->id)->first();

        // saunasテーブルと外部キー設定しているテーブル情報の取得
        $facilityTypes = FacilityType::select('id', 'type_name')->where('delete_flag', 0)->get();
        $usageTypes = UsageType::select('id', 'type_name')->where('delete_flag', 0)->get();
        $prefecture = Prefecture::select('id', 'name')->where('delete_flag', 0)->get();

        // sauna_infosテーブルと外部キー設定しているテーブル情報の取得
        $saunaTypes = SaunaType::select('id', 'type_name')->where('delete_flag', 0)->get();
        $stoveTypes = StoveType::select('id', 'type_name')->where('delete_flag', 0)->get();
        $heatTypes = HeatType::select('id', 'source_name')->where('delete_flag', 0)->get();

        // water_bathsテーブルと外部キー設定しているテーブル情報の取得
        $waterTypes = WaterType::select('id', 'type_name')->where('delete_flag', 0)->get();
        $bathTypes = BathType::select('id', 'type_name')->where('delete_flag', 0)->get();
    
        // images_facilitiesテーブルと外部キー設定しているテーブル情報の取得
        // business_hoursテーブルと外部キー設定しているテーブル情報の取得
    
    
        // ビューにデータを渡す
        return inertia('Sauna/Edit', [
          'sauna' => $sauna,
          'saunaInfo' => $saunaInfo,
          'waterBath' => $waterBath,
          'facilityTypes' => $facilityTypes,
          'usageTypes' => $usageTypes,
          'prefectures' => $prefecture,
          'saunaTypes' => $saunaTypes,
          'stoveTypes' => $stoveTypes,
          'heatTypes' => $heatTypes,
          'waterTypes' => $waterTypes,
          'bathTypes' => $bathTypes,
      ]);

  }

  // 更新処理
  public function update(SaunaRequest $request, $id){
    // バリデーション後のリクエストの受け取り(SaunaRequest)
    // Create.vueからフォームを受け取り、sauna_infosテーブル、water_bathsテーブル、business_hoursテーブル、images_facilitiesテーブルに振り分ける
    // リクエストからデータを取得し、カラム名を変更
    $saunaData = $request->only([
      'user_id','facility_name', 'facility_type_id', 'usage_type_id', 'prefecture_id',
      'address1', 'address2', 'address3', 'access_text', 'tel', 'website_url',
      'business_hours_detail', 'min_fee', 'fee_text'
    ]);

    $saunaInfoData = $request->only([
        'sauna_type_id', 'stove_type_id', 'heat_type_id', 'temperature_sauna',
        'capacity_sauna', 'additional_info_sauna'
    ]);

    $waterBathData = $request->only([
        'bath_type_id', 'water_type_id', 'temperature_water', 'capacity_water',
        'deep_water', 'additional_info_water'
    ]);

    // フォームで受け取ったカラム名をDBと合わせる
    $saunaInfoKeyMap = [
      'temperature_sauna' => 'temperature',
      'capacity_sauna' => 'capacity',
      'additional_info_sauna' => 'additional_info',
    ];
    
    foreach ($saunaInfoKeyMap as $oldKey => $newKey) {
      if (isset($saunaInfoData[$oldKey])) {
          $saunaInfoData[$newKey] = $saunaInfoData[$oldKey];
          unset($saunaInfoData[$oldKey]);
      }
    }
    
    $waterBathKeyMap = [
        'temperature_water' => 'temperature',
        'capacity_water' => 'capacity',
        'additional_info_water' => 'additional_info',
    ];
    
    foreach ($waterBathKeyMap as $oldKey => $newKey) {
      if (isset($waterBathData[$oldKey])) {
          $waterBathData[$newKey] = $waterBathData[$oldKey];
          unset($waterBathData[$oldKey]);
      }
    }
    
    // ログに配列を出力する
    logger('===========saunasテーブル挿入データ===========');
    logger($sauna = Sauna::find($id));
    logger('===========saunasテーブル挿入データ===========');
    logger($saunaData);
    logger('===========saunasテーブル挿入データ===========');
    logger($saunaInfoData);
    logger('===========sauna_infosテーブル挿入データ===========');
    logger($waterBathData);
    logger('===========water_bathsテーブル挿入データ===========');

    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
      // モデルを作成してデータを保存
      $sauna = Sauna::find($id);
      $sauna->update($saunaData);
      $saunaInfo = SaunaInfo::where('sauna_id', $sauna->id)->update($saunaInfoData);
      $waterBath = WaterBath::where('sauna_id', $sauna->id)->update($waterBathData);

      DB::commit();
      // 成功した場合の処理
      logger('DBの挿入が成功しました');
      return redirect('/saunas')->with('success', '登録が成功しました。');

    } catch (\Exception $e) {
        DB::rollBack();
        // エラーハンドリング
        logger('DBの挿入が失敗しました');
        logger($e->getMessage());
        return back()->withInput()->with('error', '登録中にエラーが発生しました。');
    }

    // return redirect()->route('sauna.show', $id);
  }

  // 削除
  public function destroy($id){
    return redirect()->route('saunas.index');
  }
}

