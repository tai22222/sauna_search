<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagesFacility extends Model
{
    use HasFactory;

    protected $fillable = [
      'main_image_url',
      'image1_url',
      'image2_url',
      'image3_url',
      'image4_url',
      'image5_url',
    ];
}
