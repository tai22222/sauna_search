<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaunaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(FormRequest $request)
    {
      logger($request);
      logger("==========FormRequest=============");
      return [
        'facility_name' => ['required', 'string', 'max:255'],
        'facility_type_id' => ['required', 'integer'],
        'usage_type_id' => ['required', 'integer'],
        'prefecture_id' => ['required', 'integer'],
        'address1' => ['required', 'string', 'max:255'],
        'address2' => ['nullable', 'string', 'max:255'],
        'address3' => ['nullable', 'string', 'max:255'],
        'access_text' => ['nullable', 'string'],
        'tel' => ['nullable', 'string', 'max:255'],
        'website_url' => ['nullable', 'url'],
        'business_hours_detail' => ['nullable', 'string'],
        'min_fee' => ['nullable', 'integer'],
        'fee_text' => ['nullable', 'string'],

        'sauna_type_id' => ['nullable', 'integer'],
        'stove_type_id' => ['nullable', 'integer'],
        'heat_type_id' => ['nullable', 'integer'],
        'temperature_sauna' => ['nullable', 'integer'],
        'capacity_sauna' => ['nullable', 'integer'],
        'additional_info_sauna' => ['nullable', 'string'],

        'bath_type_id' => ['nullable', 'integer'],
        'water_type_id' => ['nullable', 'integer'],
        'temperature_water' => ['nullable', 'integer'],
        'capacity_water' => ['nullable', 'integer'],
        'deep_water' => ['nullable', 'integer'],
        'additional_info_water' => ['nullable', 'string'],

        // 曜日ごとのバリデーションルールを追加
        'day_of_week_mon' => ['required', 'string'],
        'opening_time_mon' => ['nullable', 'date_format:H:i'],
        'closing_time_mon' => ['nullable', 'date_format:H:i'],
        'is_closed_mon' => ['required', 'boolean'],
        'day_of_week_tue' => ['required', 'string'],
        'opening_time_tue' => ['nullable', 'date_format:H:i'],
        'closing_time_tue' => ['nullable', 'date_format:H:i'],
        'is_closed_tue' => ['required', 'boolean'],
        'day_of_week_wed' => ['required', 'string'],
        'opening_time_wed' => ['nullable', 'date_format:H:i'],
        'closing_time_wed' => ['nullable', 'date_format:H:i'],
        'is_closed_wed' => ['required', 'boolean'],
        'day_of_week_thu' => ['required', 'string'],
        'opening_time_thu' => ['nullable', 'date_format:H:i'],
        'closing_time_thu' => ['nullable', 'date_format:H:i'],
        'is_closed_thu' => ['required', 'boolean'],
        'day_of_week_fri' => ['required', 'string'],
        'opening_time_fri' => ['nullable', 'date_format:H:i'],
        'closing_time_fri' => ['nullable', 'date_format:H:i'],
        'is_closed_fri' => ['required', 'boolean'],
        'day_of_week_sat' => ['required', 'string'],
        'opening_time_sat' => ['nullable', 'date_format:H:i'],
        'closing_time_sat' => ['nullable', 'date_format:H:i'],
        'is_closed_sat' => ['required', 'boolean'],
        'day_of_week_sun' => ['required', 'string'],
        'opening_time_sun' => ['nullable', 'date_format:H:i'],
        'closing_time_sun' => ['nullable', 'date_format:H:i'],
        'is_closed_sun' => ['required', 'boolean'],

        // 画像アップロード(一旦String型に設定)
        'main_image_url' => ['required', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        'image1_url' =>  ['nullable', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        'image2_url' =>  ['nullable', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        'image3_url' =>  ['nullable', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        'image4_url' =>  ['nullable', 'mimetypes:image/jpeg,image/png', 'max:1024'],
        'image5_url' =>  ['nullable', 'mimetypes:image/jpeg,image/png', 'max:1024'],
    ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        logger()->error('バリデーションエラーが発生しました:', $validator->errors()->all());
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
}
