<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sauna extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'facility_name',
      'facility_type_id',
      'usage_type_id',
      'gender_id',
      'prefecture_id',
      'address1',
      'address2',
      'address3',
      'access_text',
      'tel',
      'website_url',
      'business_hours_detail',
      'min_fee',
      'fee_text',
      'delete_flag'
    ];

  // サウナ情報とのリレーション(1対1)
  public function saunaInfo()
  {
      return $this->hasOne(SaunaInfo::class);
  }

  // 水風呂情報とのリレーション(1対1)
  public function waterBath()
  {
      return $this->hasOne(WaterBath::class);
  }

  // 曜日別営業時間情報とのリレーション(1対多)
  public function businessHours()
  {
      return $this->hasMany(BusinessHour::class);
  }

  // 画像情報とのリレーション(1対多)
  public function imagesFacility()
  {
      return $this->hasOne(ImagesFacility::class);
  }


  // facility_types テーブルのデータを取得(facility_typesに依存)
  public function facilityType()
  {
      // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
      return $this->belongsTo(FacilityType::class, 'facility_type_id');
  }

  // usage_types テーブルのデータを取得(usage_typesに依存)
  public function usageType()
  {
      // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
      return $this->belongsTo(UsageType::class, 'usage_type_id');
  }

  // usage_types テーブルのデータを取得(prefecturesに依存)
  public function prefecture()
  {
      // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
      return $this->belongsTo(Prefecture::class, 'prefecture_id');
  }
  
}
