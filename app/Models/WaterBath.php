<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterBath extends Model
{
    use HasFactory;

    protected $fillable = [
      'sauna_id',
      'bath_type_id',
      'water_type_id',
      'temperature',
      'capacity',
      'deep_water',
      'additional_info',
      'delete_flag'
    ];

    // water_baths テーブルと関連づけられた bath_types テーブルのデータを取得するための関係を定義する
    public function bathType()
    {
      // water_baths テーブルの bath_type_id カラムを参照して、bath_types テーブルの id カラムと関連づける
      return $this->belongsTo(BathType::class, 'bath_type_id');
    }
    // water_baths テーブルと関連づけられた water_types テーブルのデータを取得するための関係を定義する
    public function waterType()
    {
      // water_baths テーブルの water_type_id カラムを参照して、 water_types テーブルの id カラムと関連づける
      return $this->belongsTo(WaterType::class, ' water_type_id');
    }
  
}
