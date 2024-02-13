<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaunaInfo extends Model
{
    use HasFactory;

    protected $fillable = [
      'sauna_id',
      'sauna_type_id',
      'stove_type_id',
      'heat_type_id',
      'temperature',
      'capacity',
      'additional_info',
      'delete_flag'
  ];

    // sauna_infos テーブルと関連づけられた sauna_types テーブルのデータを取得するための関係を定義する
    public function saunaType()
    {
        // sauna_infos テーブルの sauna_type_id カラムを参照して、sauna_types テーブルの id カラムと関連づける
        return $this->belongsTo(SaunaType::class, 'sauna_type_id');
    }
    // sauna_infos テーブルと関連づけられた stove_types テーブルのデータを取得するための関係を定義する
    public function stoveType()
    {
        // sauna_infos テーブルの stove_type_id カラムを参照して、stove_types テーブルの id カラムと関連づける
        return $this->belongsTo(StoveType::class, 'stove_type_id');
    }
    // sauna_infos テーブルと関連づけられた heat_types テーブルのデータを取得するための関係を定義する
    public function heatType()
    {
        // sauna_infos テーブルの heat_type_id カラムを参照して、heat_types テーブルの id カラムと関連づける
        return $this->belongsTo(HeatType::class, 'heat_type_id');
    }
  
}
