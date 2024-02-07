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
  
}
