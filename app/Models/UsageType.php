<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageType extends Model
{
    use HasFactory;

  //   protected $fillable = [

  // ];

      // このモデルと関連づけられた saunas テーブルのデータを取得するための関係を定義する
      public function saunas()
      {
          // usage_types テーブルの id カラムを参照して、saunas テーブルの usage_type_id カラムと関連づける
          return $this->hasMany(Sauna::class, 'usage_type_id');
      }
}
