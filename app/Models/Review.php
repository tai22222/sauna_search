<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'sauna_id',
      'visited_date',
      'title',
      'content',
      'review_image',
    ];

  // サウナ情報とのリレーション(1対1)
  public function sauna()
  {
      return $this->belongsTo(Sauna::class);
  }

  public function user()
  {
      return $this->belongsTo(User::class);
  }

}
