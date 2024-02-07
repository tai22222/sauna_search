<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sauna extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id',
      'facility_name',
      'facility_type_id',
      'usage_type_id',
      'gender_id',
      'prefecture',
      'address1',
      'address2',
      'address3',
      'access_text',
      'tel',
      'website_url',
      'business_hours_detail',
      'min_fee',
      'fee_text',
  ];
  
}
