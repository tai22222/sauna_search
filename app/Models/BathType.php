<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BathType extends Model
{
    use HasFactory;

  //   protected $fillable = [

  // ];

      // このモデルと関連づけられた water_baths テーブルのデータを取得するための関係を定義する
      public function waterBaths()
      {
          // bath_types テーブルの id カラムを参照して、water_baths テーブルの bath_type_id カラムと関連づける
          return $this->hasMany(WaterBath::class, 'bath_type_id');
      }
}
