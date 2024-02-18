<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    use HasFactory;

    protected $fillable = [
      'sauna_id',
      'day_of_week',
      'opening_time',
      'closing_time',
      'is_closed',
  ];

  public function sauna()
  {
      return $this->belongsTo(Sauna::class);
  }

}
