<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
    ];
    }
}
