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
    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
        // サウナの施設情報を抽出
        $saunaData = $this->extractSaunaData($request);

        // サウナ情報を抽出
        $saunaInfoData = $this->extractSaunaInfoData($request);

        // 水風呂情報を抽出
        $waterBathData = $this->extractWaterBathData($request);

        // 営業時間の情報を抽出
        $businessHours = $this->extractBusinessHourData($request);

        // 画像の情報を抽出
        $imagesFacilityData = $this->extractImagesFacilityData($request);

        // サウナの施設情報をDBに挿入
        $sauna = Sauna::create($saunaData);
        $saunaId = $sauna->id;

        // サウナ情報をDBに挿入
        $saunaInfoData['sauna_id'] = $saunaId;
        $sauna->saunaInfo()->create($saunaInfoData);

        // 水風呂情報をDBに挿入
        $waterBathData['sauna_id'] = $saunaId;
        $sauna->waterBath()->create($waterBathData);

        // 営業時間の情報をDBに挿入
        $this->processBusinessHourData($businessHours, $sauna->id);

        // 画像の情報をDBに挿入
        $imagesFacilityData['sauna_id'] = $saunaId;
        $sauna->imagesFacilities()->create($imagesFacilityData);

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

  // サウナの各情報(サウナの施設情報)
  private function extractSaunaData(SaunaRequest $request)
  {
      return $request->only([
          'user_id','facility_name', 'facility_type_id', 'usage_type_id', 'prefecture_id',
          'address1', 'address2', 'address3', 'access_text', 'tel', 'website_url',
          'business_hours_detail', 'min_fee', 'fee_text'
      ]);
  }

  // サウナ情報
  private function extractSaunaInfoData(SaunaRequest $request)
  {
      $saunaInfoData = $request->only([
          'sauna_type_id', 'stove_type_id', 'heat_type_id', 'temperature_sauna',
          'capacity_sauna', 'additional_info_sauna'
      ]);

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
      return $saunaInfoData;
  }

  // 水風呂情報
  private function extractWaterBathData(SaunaRequest $request)
  {
      $waterBathData = $request->only([
          'bath_type_id', 'water_type_id', 'temperature_water', 'capacity_water',
          'deep_water', 'additional_info_water'
      ]);

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
    return $waterBathData;
  }

  // 営業時間の情報
  private function extractBusinessHourData(SaunaRequest $request)
  {
      $businessHourData = $request->only([
          'day_of_week_mon', 'opening_time_mon', 'closing_time_mon', 'is_closed_mon',
          'day_of_week_tue', 'opening_time_tue', 'closing_time_tue', 'is_closed_tue',
          'day_of_week_wed', 'opening_time_wed', 'closing_time_wed', 'is_closed_wed',
          'day_of_week_thu', 'opening_time_thu', 'closing_time_thu', 'is_closed_thu',
          'day_of_week_fri', 'opening_time_fri', 'closing_time_fri', 'is_closed_fri',
          'day_of_week_sat', 'opening_time_sat', 'closing_time_sat', 'is_closed_sat',
          'day_of_week_sun', 'opening_time_sun', 'closing_time_sun', 'is_closed_sun',
      ]);

      $businessHours = [];

      $dayOfWeeks = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

      foreach ($dayOfWeeks as $day) {
          $keyMap = [
              "day_of_week_$day" => 'day_of_week',
              "opening_time_$day" => 'opening_time',
              "closing_time_$day" => 'closing_time',
              "is_closed_$day" => 'is_closed',
          ];

          $businessHour = [];
          
          foreach ($keyMap as $oldKey => $newKey) {
              if (isset($businessHourData[$oldKey])) {
                  $value = $businessHourData[$oldKey];
                  // 時刻の値をフォーマットして MySQL の時間型に変換
                  if (strpos($oldKey, 'opening_time') !== false || strpos($oldKey, 'closing_time') !== false) {
                      $value = date('H:i', strtotime($value));
                  }
                  $businessHour[$newKey] = $value;
              }
          }

          $businessHours[] = $businessHour;
      }

      return $businessHours;
  }

  // 営業時間の各曜日ごとのDB挿入メソッド
  protected function processBusinessHourData($businessHours, $saunaId)
  {
      foreach ($businessHours as $businessHour) {
          // ビジネスアワーのデータにサウナIDを追加してデータベースに挿入
          $businessHour['sauna_id'] = $saunaId;
          
          // BusinessHourモデルを使ってデータベースに新しいレコードを挿入
          BusinessHour::create($businessHour);
      }
  }

  // 画像の情報(画像ファイルの取得から保存先の取得) todo publicにするべき？
  private function extractImagesFacilityData(SaunaRequest $request)
  {
      $imagesFacilityData = [];

      // メイン画像の処理
      if ($request->hasFile('main_image_url')) {
          $mainImagePath = $request->file('main_image_url')->store('sauna-images', 'public');
          $imagesFacilityData['main_image_url'] = $mainImagePath;
      }
      
      // 他の画像ファイルの処理
      $imageKeys = ['image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url'];
      foreach ($imageKeys as $key) {
          if ($request->hasFile($key)) {
              $imagePath = $request->file($key)->store('sauna-images', 'public');
              $imagesFacilityData[$key] = $imagePath;
          }
      }

      return $imagesFacilityData;
  }

  // 詳細表示
  public function show($id){
    $sauna = Sauna::findOrFail($id);

    return Inertia::render('Sauna/Info');
  }

  // 詳細編集画面表示
  public function edit($id){
        $sauna = Sauna::with('facilityType', 'usageType', 'prefecture')->findOrFail($id);
            
        $saunaInfo = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->where('sauna_id', $sauna->id)->first();
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
        $imagesFacilities = ImagesFacility::select('id', 'main_image_url', 'image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url')->where('sauna_id', $sauna->id)->get();
        // business_hoursテーブルと外部キー設定しているテーブル情報の取得
        $businessHours = BusinessHour::select('id', 'day_of_week', 'opening_time', 'closing_time', 'is_closed')->where('sauna_id', $sauna->id)->get();
    
    
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
          'imagesFacilities' => $imagesFacilities,
          'businessHours' => $businessHours,
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

