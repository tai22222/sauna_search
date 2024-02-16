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

use App\Models\BusinessHour;
use App\Models\ImagesFacility;

// フォームのバリデーション(ミドルウェア)
use App\Http\Requests\SaunaRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaunaController extends Controller
{
  // 一覧表示
  public function index(){
    $saunas = Sauna::with('facilityType','usageType', 'prefecture')->get();
    $saunaInfos = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->get();
    $waterBaths = WaterBath::with('bathType', 'waterType')->get();
    
    return Inertia::render('Sauna/Index', [
      'saunas' => $saunas,
      'saunaInfos' => $saunaInfos,
      'waterBaths' => $waterBaths,
    ]);
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
    $businessHours = BusinessHour::select('sauna_id', 'day_of_week', 'opening_time', 'closing_time', 'is_closed')->get();

    // 受け渡し量が多いため、選択肢で使用するデータをまとめる
    $optionData = [$facilityTypes, $usageTypes, $prefecture, $saunaTypes, $stoveTypes, $heatTypes, $waterTypes, $bathTypes];

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
      'businessHours' => $businessHours,
    ]);
  }

  // 新規作成処理
  public function store(SaunaRequest $request){
    // Create.vueからSaunaRequestでバリデーション後の$requestの受け取り
    // saunasテーブル、sauna_infosテーブル、water_bathsテーブル、business_hoursテーブル、images_facilitiesテーブルに振り分ける
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

    $businessHourData = $request->only([
      'day_of_week_mon', 'opening_time_mon', 'closing_time_mon', 'is_closed_mon',
      'day_of_week_tue', 'opening_time_tue', 'closing_time_tue', 'is_closed_tue',
      'day_of_week_wed', 'opening_time_wed', 'closing_time_wed', 'is_closed_wed',
      'day_of_week_thu', 'opening_time_thu', 'closing_time_thu', 'is_closed_thu',
      'day_of_week_fri', 'opening_time_fri', 'closing_time_fri', 'is_closed_fri',
      'day_of_week_sat', 'opening_time_sat', 'closing_time_sat', 'is_closed_sat',
      'day_of_week_sun', 'opening_time_sun', 'closing_time_sun', 'is_closed_sun',
    ]);

    if ($request->hasFile('main_image_url')) {
      // ファイルのアップロード処理を行う前に、ファイルを取得し、保存先のパスを取得する
      $mainImagePath = $request->file('main_image_url')->store('sauna-images', 'public');
  
      // データを準備（ファイルのパスを含める）
      $imagesFacilityData = $request->only([
          'image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url',
      ]);
      $imagesFacilityData['main_image_url'] = $mainImagePath;
  }

    // フォームで受け取ったカラム名をDBと合わせる(サウナ情報と水風呂情報に関して)
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

    // 曜日ごとの営業時間を変数に詰め替えとtime型以外で渡ってきた時にtime型に変換
    $monBusinessHourKeyMap = [
      'day_of_week_mon' => 'day_of_week', 
      'opening_time_mon' => 'opening_time', 
      'closing_time_mon' => 'closing_time', 
      'is_closed_mon' => 'is_closed',
    ];

    $monBusinessHourData = [];

    foreach ($monBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_mon' || $oldKey === 'closing_time_mon') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $monBusinessHourData[$newKey] = $formattedValue;
        }
    }

    $tueBusinessHourKeyMap = [
      'day_of_week_tue' => 'day_of_week', 
      'opening_time_tue' => 'opening_time', 
      'closing_time_tue' => 'closing_time', 
      'is_closed_tue' => 'is_closed',
    ];

    $tueBusinessHourData = [];

    foreach ($tueBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_tue' || $oldKey === 'closing_time_tue') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $tueBusinessHourData[$newKey] = $formattedValue;
        }
    }

    $wedBusinessHourKeyMap = [
      'day_of_week_wed' => 'day_of_week', 
      'opening_time_wed' => 'opening_time', 
      'closing_time_wed' => 'closing_time', 
      'is_closed_wed' => 'is_closed',
    ];

    $wedBusinessHourData = [];

    foreach ($wedBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_wed' || $oldKey === 'closing_time_wed') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $wedBusinessHourData[$newKey] = $formattedValue;
        }
    }

    $thuBusinessHourKeyMap = [
      'day_of_week_thu' => 'day_of_week', 
      'opening_time_thu' => 'opening_time', 
      'closing_time_thu' => 'closing_time', 
      'is_closed_thu' => 'is_closed',
    ];

    $thuBusinessHourData = [];

    foreach ($thuBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_thu' || $oldKey === 'closing_time_thu') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $thuBusinessHourData[$newKey] = $formattedValue;
        }
    }
    
    $friBusinessHourKeyMap = [
      'day_of_week_fri' => 'day_of_week', 
      'opening_time_fri' => 'opening_time', 
      'closing_time_fri' => 'closing_time', 
      'is_closed_fri' => 'is_closed',
    ];

    $friBusinessHourData = [];

    foreach ($friBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_fri' || $oldKey === 'closing_time_fri') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $friBusinessHourData[$newKey] = $formattedValue;
        }
    }

    $satBusinessHourKeyMap = [
      'day_of_week_sat' => 'day_of_week', 
      'opening_time_sat' => 'opening_time', 
      'closing_time_sat' => 'closing_time', 
      'is_closed_sat' => 'is_closed',
    ];

    $satBusinessHourData = [];

    foreach ($satBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_sat' || $oldKey === 'closing_time_sat') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $satBusinessHourData[$newKey] = $formattedValue;
        }
    }

    $sunBusinessHourKeyMap = [
      'day_of_week_sun' => 'day_of_week', 
      'opening_time_sun' => 'opening_time', 
      'closing_time_sun' => 'closing_time', 
      'is_closed_sun' => 'is_closed',
    ];

    $sunBusinessHourData = [];

    foreach ($sunBusinessHourKeyMap as $oldKey => $newKey) {
        if (isset($businessHourData[$oldKey])) {
            $value = $businessHourData[$oldKey];
            // 時刻の値をフォーマットして MySQL の時間型に変換
            if ($oldKey === 'opening_time_sun' || $oldKey === 'closing_time_sun') {
                $formattedValue = date('H:i', strtotime($value));
            } else {
                $formattedValue = $value;
            }
            $sunBusinessHourData[$newKey] = $formattedValue;
        }
    }


    // ログに配列を出力する
    logger($saunaData);
    logger('===========saunasテーブル挿入データ===========');
    logger($saunaInfoData);
    logger('===========sauna_infosテーブル挿入データ===========');
    logger($waterBathData);
    logger('===========water_bathsテーブル挿入データ===========');
    logger($businessHourData);
    logger('===========business_hoursテーブル挿入データ===========');
    // logger($monBusinessHourData);
    // logger('===========月曜挿入データ===========');
    // logger($tueBusinessHourData);
    // logger('===========火曜テーブル挿入データ===========');
    // logger($wedBusinessHourData);
    // logger('===========水曜テーブル挿入データ===========');

    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
      // モデルを作成してデータを保存
      $sauna = Sauna::create($saunaData);
      $saunaInfo = SaunaInfo::create(array_merge($saunaInfoData, ['sauna_id' => $sauna->id]));
      $waterBath = WaterBath::create(array_merge($waterBathData, ['sauna_id' => $sauna->id]));
      $monBusinessHourData = BusinessHour::create(array_merge($monBusinessHourData, ['sauna_id' => $sauna->id]));
      $tueBusinessHourData = BusinessHour::create(array_merge($tueBusinessHourData, ['sauna_id' => $sauna->id]));
      $wedBusinessHourData = BusinessHour::create(array_merge($wedBusinessHourData, ['sauna_id' => $sauna->id]));
      $thuBusinessHourData = BusinessHour::create(array_merge($thuBusinessHourData, ['sauna_id' => $sauna->id]));
      $friBusinessHourData = BusinessHour::create(array_merge($friBusinessHourData, ['sauna_id' => $sauna->id]));
      $satBusinessHourData = BusinessHour::create(array_merge($satBusinessHourData, ['sauna_id' => $sauna->id]));
      $sunBusinessHourData = BusinessHour::create(array_merge($sunBusinessHourData, ['sauna_id' => $sauna->id]));

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

