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

    // saunas テーブルと関連づけられた facility_types テーブルのデータを取得するための関係を定義する
    public function facilityType()
    {
        // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
        return $this->belongsTo(FacilityType::class, 'facility_type_id');
    }

    // saunas テーブルと関連づけられた usage_types テーブルのデータを取得するための関係を定義する
    public function usageType()
    {
        // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
        return $this->belongsTo(UsageType::class, 'usage_type_id');
    }

    // saunas テーブルと関連づけられた usage_types テーブルのデータを取得するための関係を定義する
    public function prefecture()
    {
        // saunas テーブルの facility_type_id カラムを参照して、facility_types テーブルの id カラムと関連づける
        return $this->belongsTo(Prefecture::class, 'prefecture_id');
    }
  
}
