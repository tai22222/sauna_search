<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasFactory;

  //   protected $fillable = [

  // ];

      // このモデルと関連づけられた users テーブルのデータを取得するための関係を定義する
      public function users()
      {
          // genders テーブルの id カラムを参照して、users テーブルの gender_id カラムと関連づける
          return $this->hasMany(User::class);
      }
}
