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
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Carbon;

class SaunaController extends Controller
{
  // 一覧表示
  public function index(Request $request){
    logger('$requestの中身');
    logger($request);
    $prefectures = Prefecture::select('id', 'name')->where('delete_flag', 0)->get();
    // サウナ情報のクエリを取得
    $query = Sauna::select('id', 'facility_name', 'facility_type_id', 'usage_type_id', 'prefecture_id', 'address1', 'min_fee', 'created_at', 'updated_at');

    // 検索結果の取得
    $prefecture = $request->input('prefecture');
    // condition = $request->input('condition');
    // keyword = $request->input('keyword');
    $sauna_temperature_from = $request->input('sauna_temperature_from');
    $sauna_temperature_to = $request->input('sauna_temperature_to');
    $water_bath_temperature_from = $request->input('water_bath_temperature_from');
    $water_bath_temperature_to = $request->input('water_bath_temperature_to');

    $sort = $request->input('sort');
    $order = $request->input('order');


    // 検索条件がある場合はフィルタリングを行う
    if ($request->has('sort') && $request->has('order')) {
        $query->orderBy($request->sort, $request->order);
    }

    // 都道府県に関する検索条件
    if ($request->filled('prefecture')) {
      $query->where('prefecture_id', $prefecture);
    }

    // サウナの温度に関する検索条件
    if ($request->filled('sauna_temperature_from') && !$request->filled('sauna_temperature_to')) {
      // _fromが存在して_toが空の場合
      $query->whereHas('saunaInfo', function($query) use ($sauna_temperature_from) {
          $query->where('temperature', '>=', $sauna_temperature_from);
      });
    } elseif (!$request->filled('sauna_temperature_from') && $request->filled('sauna_temperature_to')) {
      // _fromが空で_toが存在している場合
      $query->whereHas('saunaInfo', function($query) use ($sauna_temperature_to) {
          $query->where('temperature', '<=', $sauna_temperature_to);
      });
    } elseif ($request->filled('sauna_temperature_from') && $request->filled('sauna_temperature_to')) {
      // 両方の値が存在している場合
      $query->whereHas('saunaInfo', function($query) use ($sauna_temperature_from, $sauna_temperature_to) {
          $query->whereBetween('temperature', [$sauna_temperature_from, $sauna_temperature_to]);
      });
    }

    // 水風呂の温度に関する検索条件
    if ($request->filled('water_bath_temperature_from') && !$request->filled('water_bath_temperature_to')) {
      // _fromが存在して_toが空の場合
      $query->whereHas('waterBath', function($query) use ($water_bath_temperature_from) {
          $query->where('temperature', '>=', $water_bath_temperature_from);
      });
    } elseif (!$request->filled('water_bath_temperature_from') && $request->filled('water_bath_temperature_to')) {
      // _fromが空で_toが存在している場合
      $query->whereHas('waterBath', function($query) use ($water_bath_temperature_to) {
          $query->where('temperature', '<=', $water_bath_temperature_to);
      });
    } elseif ($request->filled('water_bath_temperature_from') && $request->filled('water_bath_temperature_to')) {
      // 両方の値が存在している場合
      $query->whereHas('waterBath', function($query) use ($water_bath_temperature_from, $water_bath_temperature_to) {
          $query->whereBetween('temperature', [$water_bath_temperature_from, $water_bath_temperature_to]);
      });
    }

    // ソート条件の適用
    if ($request->filled('sort') && $request->filled('order')) {
      $query->orderBy($sort, $order);
    }

    // ソート条件がある場合は並び替えを行う
    // if ($request->has('sort')) {
    //     $sort = $request->input('sort');
    //     $query->orderBy($sort);
    // }

    // ページネーションを適用して5件ずつ表示
    $saunas = $query->with([
      'facilityType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'usageType' => function ($query) {
          $query->select('id', 'type_name');
      },
      'prefecture' => function ($query) {
          $query->select('id', 'name');
      },
      'saunaInfo' => function ($query) {
          $query->select('id', 'sauna_id', 'temperature');
      },
      'waterBath' => function ($query) {
          $query->select('id', 'sauna_id','temperature');
      },
      'businessHours' => function ($query) {
          $query->select('id', 'sauna_id','day_of_week', 'opening_time', 'closing_time', 'is_closed');
      },
      'imagesFacility' => function ($query) {
          $query->select('id', 'sauna_id','main_image_url');
      },
    ])->paginate(5);

    $data = [
      'saunas' => $saunas,
      'filters' => [
        'prefecture' => $prefecture,
        'sauna_temperature_from' => $sauna_temperature_from,
        'sauna_temperature_to' => $sauna_temperature_to,
        'water_bath_temperature_from' => $water_bath_temperature_from,
        'water_bath_temperature_to' => $water_bath_temperature_to,
      ]
    ];
    logger('$data');
    logger()->info('Sending data to Vue component:', $data);

    return Inertia::render('Sauna/Index', [
      'saunas' => $saunas,  // 検索結果のデータ
      'prefectures' => $prefectures,  // 都道府県データ
      // 検索項目のデータ
      'filters' => [
        'prefecture' => $prefecture,
        'sauna_temperature_from' => $sauna_temperature_from,
        'sauna_temperature_to' => $sauna_temperature_to,
        'water_bath_temperature_from' => $water_bath_temperature_from,
        'water_bath_temperature_to' => $water_bath_temperature_to,
        // ソートデータ
        'sort' => $sort,
        'order' => $order,
      ]
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

    // $saunaInfos = SaunaInfo::with('saunaType', 'stoveType', 'heatType')->get();

    // water_bathsテーブルと外部キー設定しているテーブル情報の取得
    $waterTypes = WaterType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $bathTypes = BathType::select('id', 'type_name')->where('delete_flag', 0)->get();

    // $waterBaths = WaterBath::with('bathType', 'waterType')->get();

    // images_facilitiesテーブルと外部キー設定しているテーブル情報の取得
    // business_hoursテーブルと外部キー設定しているテーブル情報の取得
    // $businessHours = BusinessHour::select('sauna_id', 'day_of_week', 'opening_time', 'closing_time', 'is_closed')->get();

    // 受け渡し量が多いため、選択肢で使用するデータをまとめる
    // $optionData = [$facilityTypes, $usageTypes, $prefecture, $saunaTypes, $stoveTypes, $heatTypes, $waterTypes, $bathTypes];

    // Create.vueに渡すオブジェクト・配列
    return inertia('Sauna/Create', [
      'saunas' => $saunas,
      'facilityTypes' => $facilityTypes,
      'usageTypes' => $usageTypes,
      'prefectures' => $prefecture,
      // 'saunaInfos' => $saunaInfos,
      'saunaTypes' => $saunaTypes,
      'stoveTypes' => $stoveTypes,
      'heatTypes' => $heatTypes,
      // 'waterBaths' => $waterBaths,
      'waterTypes' => $waterTypes,
      'bathTypes' => $bathTypes,
      // 'businessHours' => $businessHours,
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
        $this->processBusinessHourData($businessHours, $saunaId);

        // 画像の情報をDBに挿入
        $imagesFacilityData['sauna_id'] = $saunaId;
        $sauna->imagesFacility()->create($imagesFacilityData);

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

  // 営業時間の各曜日ごとのDB挿入メソッド(post)
  protected function processBusinessHourData($businessHours, $saunaId)
  {
      foreach ($businessHours as $businessHour) {
          // ビジネスアワーのデータにサウナIDを追加してデータベースに挿入
          $businessHour['sauna_id'] = $saunaId;
          
          // BusinessHourモデルを使ってデータベースに新しいレコードを挿入
          BusinessHour::create($businessHour);
      }
  }

  // 営業時間の各曜日ごとのDB挿入メソッド(put)
  protected function updateBusinessHourData($businessHours, $saunaId)
  {

      foreach ($businessHours as $businessHour) {

        // is_closedをboolean型に変換
        $businessHour['is_closed'] = filter_var($businessHour['is_closed'], FILTER_VALIDATE_BOOLEAN);

        // ビジネスアワーのデータにサウナIDを追加してデータベースに挿入
        $businessHour['sauna_id'] = $saunaId;
          
        // BusinessHourモデルを使ってデータベースを更新
        $businessHourColumn = $businessHour['day_of_week']; // 更新するビジネスアワーの曜日を取得
        // unset($businessHour['id']); // IDは更新対象外とするため削除

        // logger('各曜日のputでの挿入メソッド');
        BusinessHour::where( 'sauna_id', $saunaId)->where( 'day_of_week', $businessHourColumn)->update($businessHour);
      }
  }

  // 画像の情報(画像ファイルの取得から保存先の取得) (storeからのpostとupdateからのpostは通したい)→putは通したくない
  private function extractImagesFacilityData(SaunaRequest $request)
  {
      $imagesFacilityData = [];

      // メイン画像の処理
      if ($request->hasFile('main_image_url')) {
          $mainImagePath = $request->file('main_image_url')->store('sauna-images', 'public');
          $imagesFacilityData['main_image_url'] = $mainImagePath;
      }
      logger('メインイメージパス');
      // logger($mainImagePath);
      logger('メインイメージパス');
      // 他の画像ファイルの処理
      $imageKeys = ['image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url'];
      foreach ($imageKeys as $key) {
        logger('$imageKeys');
        logger($imageKeys);
        logger($key);
        logger('$key');
          if ($request->hasFile($key)) {
              $imagePath = $request->file($key)->store('sauna-images', 'public');
              $imagesFacilityData[$key] = $imagePath;
              logger('その他メージパス');
              logger($imagePath);
          }
      }

      return $imagesFacilityData;
  }

  // 詳細表示
  public function show($id){
    $sauna = Sauna::select('id', 'facility_name', 'facility_type_id', 'usage_type_id', 'prefecture_id', 'address1', 'address2' , 'address3', 'access_text','tel', 'website_url', 'business_hours_detail', 'min_fee', 'fee_text');

    $sauna = $sauna->with([
      'facilityType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'usageType' => function ($query) {
          $query->select('id', 'type_name');
      },
      'prefecture' => function ($query) {
          $query->select('id', 'name');
      },
      'saunaInfo' => function ($query) {
          $query->select('id', 'sauna_id', 'sauna_type_id', 'stove_type_id', 'heat_type_id', 'temperature', 'capacity', 'additional_info');
      },
      'saunaInfo.saunaType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'saunaInfo.stoveType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'saunaInfo.heatType' => function ($query) {
        $query->select('id', 'source_name');
      },
      'waterBath' => function ($query) {
          $query->select('id', 'sauna_id', 'bath_type_id', 'water_type_id', 'temperature', 'capacity', 'deep_water', 'additional_info');
      },
      'waterBath.waterType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'waterBath.bathType' => function ($query) {
        $query->select('id', 'type_name');
      },
      'businessHours' => function ($query) {
          $query->select('id', 'sauna_id','day_of_week', 'opening_time', 'closing_time', 'is_closed');
      },
      'imagesFacility' => function ($query) {
          $query->select('id', 'sauna_id','main_image_url', 'image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url');
      },
    ])->findOrFail($id);

    logger($sauna);
    return Inertia::render('Sauna/Show', [
      'sauna' => $sauna,
    ]);
  }

  // 詳細編集画面表示
  public function edit($id){
    $sauna = Sauna::with(['facilityType', 'usageType', 'prefecture'])->findOrFail($id);

    $saunaInfo = $sauna->saunaInfo()->with(['saunaType', 'stoveType', 'heatType'])->first();
    $waterBath = $sauna->waterBath()->with(['bathType', 'waterType'])->first();
    
    // saunasテーブルと外部キー設定しているテーブル情報の取得
    $facilityTypes = FacilityType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $usageTypes = UsageType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $prefectures = Prefecture::select('id', 'name')->where('delete_flag', 0)->get();
    
    // sauna_infosテーブルと外部キー設定しているテーブル情報の取得
    $saunaTypes = SaunaType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $stoveTypes = StoveType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $heatTypes = HeatType::select('id', 'source_name')->where('delete_flag', 0)->get();
    
    // water_bathsテーブルと外部キー設定しているテーブル情報の取得
    $waterTypes = WaterType::select('id', 'type_name')->where('delete_flag', 0)->get();
    $bathTypes = BathType::select('id', 'type_name')->where('delete_flag', 0)->get();
    
    // images_facilitiesテーブルと外部キー設定しているテーブル情報の取得
    $imagesFacilities = $sauna->imagesFacility()->select('id', 'main_image_url', 'image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url')->get();
    
    // business_hoursテーブルと外部キー設定しているテーブル情報の取得
    $businessHours = $sauna->businessHours()->select('id', 'day_of_week', 'opening_time', 'closing_time', 'is_closed')->get();
    // 時刻をH:i形式に整形
    foreach ($businessHours as $hour) {
      if ($hour->opening_time !== null) {
        $hour->opening_time = Carbon::createFromFormat('H:i:s', $hour->opening_time)->format('H:i');
      }
    
      if ($hour->closing_time !== null) {
          $hour->closing_time = Carbon::createFromFormat('H:i:s', $hour->closing_time)->format('H:i');
      }
    }
    
    // storeで保存した画像データをeditへ返す
    $mainImagePath = $imagesFacilities[0]['main_image_url'];
    $image1Path = $imagesFacilities[0]['image1_url'];
    $image2Path = $imagesFacilities[0]['image2_url'];
    $image3Path = $imagesFacilities[0]['image3_url'];
    $image4Path = $imagesFacilities[0]['image4_url'];
    $image5Path = $imagesFacilities[0]['image5_url'];

    
    // ビューにデータを渡す
    return inertia('Sauna/Edit', [
      'sauna' => $sauna,
      'saunaInfo' => $saunaInfo,
      'waterBath' => $waterBath,
      'facilityTypes' => $facilityTypes,
      'usageTypes' => $usageTypes,
      'prefectures' => $prefectures,
      'saunaTypes' => $saunaTypes,
      'stoveTypes' => $stoveTypes,
      'heatTypes' => $heatTypes,
      'waterTypes' => $waterTypes,
      'bathTypes' => $bathTypes,
      'imagesFacilities' => $imagesFacilities,
      'businessHours' => $businessHours,
      'mainImagePath' => $mainImagePath,
      'image1Path' => $image1Path,
      'image2Path' => $image2Path,
      'image3Path' => $image3Path,
      'image4Path' => $image4Path,
      'image5Path' => $image5Path,
    ]);

  }

  // 更新処理
  public function update(SaunaRequest $request, $id){

    // DBの挿入とトランザクションの設定
    DB::beginTransaction();
    try {
      // モデルを作成してデータを保存
      $sauna = Sauna::find($id);

        // サウナの施設情報を抽出
        $saunaData = $this->extractSaunaData($request);

        // サウナ情報を抽出
        $saunaInfoData = $this->extractSaunaInfoData($request);

        // 水風呂情報を抽出
        $waterBathData = $this->extractWaterBathData($request);

        // 営業時間の情報を抽出
        $businessHours = $this->extractBusinessHourData($request);

        // サウナの施設情報をDBに挿入
        $sauna->update($saunaData);

        // サウナ情報をDBに挿入
        $saunaInfoData['sauna_id'] = $id;
        logger('サウナ情報');
        logger($saunaInfoData);
        $sauna->saunaInfo()->update($saunaInfoData);

        // 水風呂情報をDBに挿入
        $waterBathData['sauna_id'] = $id;
        logger('水風呂');
        logger($waterBathData);
        $sauna->waterBath()->update($waterBathData);

        // 営業時間の情報をDBに挿入
        $this->updateBusinessHourData($businessHours, $id);

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

  // 削除
  public function destroy($id){
    return redirect()->route('saunas.index');
  }

  // axiosでedit画面表示時にDBデータを取得
  public function getImage($id)
  {
      // 指定されたIDに対応するサウナの画像情報を取得
      $sauna = Sauna::findOrFail($id);
      $imagesFacilities = $sauna->imagesFacility()->select('id', 'main_image_url', 'image1_url', 'image2_url', 'image3_url', 'image4_url', 'image5_url')->get();
      // 必要なデータを抽出して返す
      return response()->json([
          'main_image_url' => $imagesFacilities[0]['main_image_url'],
          'image1_url' => $imagesFacilities[0]['image1_url'],
          'image2_url' => $imagesFacilities[0]['image2_url'],
          'image3_url' => $imagesFacilities[0]['image3_url'],
          'image4_url' => $imagesFacilities[0]['image4_url'],
          'image5_url' => $imagesFacilities[0]['image5_url']
      ]);
  }

  // 画像更新の際のアクション
  public function updateImage(SaunaRequest $request ,$id)
  {
    logger('post時の画像データをDBへ挿入');
    logger($request);

    DB::beginTransaction();
    try {
      // モデルを作成してデータを保存
      $sauna = Sauna::find($id);

      // 画像の情報を抽出(putでは通したくないから別で切り分け)
      $imagesFacilityData = $this->extractImagesFacilityData($request);
      logger($imagesFacilityData);

      // 画像の情報をDBに挿入
      $imagesFacilityData['sauna_id'] = $id;
      $sauna->imagesFacility()->update($imagesFacilityData);

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

}


