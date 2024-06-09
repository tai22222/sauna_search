<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender_id',
        'birth_year',
        'birth_month',
        'birth_day',
        'debut_year',
        'debut_month',
        'home_sauna',
        'profile_text',
        'delete_flag'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    // users テーブルと関連づけられた genders テーブルのデータを取得するための関係を定義する
    public function gender()
    {
        // users テーブルの gender_id カラムを参照して、genders テーブルの id カラムと関連づける
        return $this->belongsTo(Gender::class);
    }

    // レビュー情報とのリレーション(1対多)
    public function review()
    {
        return $this->hasMany(Review::class);
    }

    // お気に入り情報に必要なリレーション
    public function favoriteSaunas()
    {
        return $this->belongsToMany(Sauna::class, 'favorites_facilities', 'user_id', 'sauna_id');
    }
}
