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
  
}
