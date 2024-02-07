<?php

namespace Database\Factories;

use App\Models\Sauna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;

class SaunaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sauna::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 3),
            'facility_name' => $this->faker->company,
            'facility_type_id' => $this->faker->numberBetween(1, 10),
            'usage_type_id' => $this->faker->numberBetween(1, 3),
            'gender_id' => $this->faker->numberBetween(1, 2),
            'prefecture' => $this->faker->numberBetween(1, 47),
            'address1' => $this->faker->city,
            'address2' => $this->faker->word,
            'address3' => $this->faker->address,
            'access_text' => $this->faker->text,
            'tel' => $this->faker->phoneNumber,
            'website_url' => $this->faker->url,
            'business_hours_detail' => $this->faker->text,
            'min_fee' => $this->faker->numberBetween(100,3000),
            'fee_text' => $this->faker->text,
        ];
    }
}
