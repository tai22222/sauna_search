<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaunaType extends Model
{
    use HasFactory;

  //   protected $fillable = [

  // ];

      // このモデルと関連づけられた sauna_infos テーブルのデータを取得するための関係を定義する
      public function saunaInfos()
      {
          // sauna_types テーブルの id カラムを参照して、sauna_infos テーブルの sauna_type_id カラムと関連づける
          return $this->hasMany(SaunaInfo::class, 'sauna_type_id');
      }
}
