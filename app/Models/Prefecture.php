<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    use HasFactory;

  //   protected $fillable = [

  // ];

      // このモデルと関連づけられた saunas テーブルのデータを取得するための関係を定義する
      public function saunas()
      {
          // prefectures テーブルの id カラムを参照して、saunas テーブルの prefecture_id カラムと関連づける
          return $this->hasMany(Sauna::class, 'prefecture_id');
      }
}
