<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
          'name' => ['required', 'string', 'max:255'],
          'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
          'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
          'gender_id' => ['nullable', 'integer'],
          'birth_year' => ['nullable', 'integer'],
          'birth_month' => ['nullable', 'integer'],
          'birth_day' => ['nullable', 'integer'],
          'debut_year' => ['nullable', 'integer'],
          'debut_month' => ['nullable', 'integer'],
          'home_sauna' => ['nullable', 'string', 'max:255'],
          'profile_text' => ['nullable', 'string'],
        ])->validateWithBag('updateProfileInformation');

        // 入力項目に対する処理
        $this->updateAdditionalFields($user, $input);

        // 変更された画像情報を取得して更新
        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        // メールアドレスがユーザ本人のものかの確認
        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }

        logger($input);
    }

    // メソッド
    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    // 入力項目の処理
    protected function updateAdditionalFields(User $user, array $input): void
    {
        $fields = ['gender_id', 'birth_year', 'birth_month', 'birth_day', 'debut_year', 'debut_month', 'home_sauna', 'profile_text'];

        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $user->$field = $input[$field];
            }
        }
    }
}
