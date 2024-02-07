<?php

namespace Database\Factories;

use App\Models\SaunaInfo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;


class SaunaInfoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SaunaInfo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sauna_id' => $this->faker->unique()->numberBetween(5, 14),
            'sauna_type_id' => $this->faker->numberBetween(1, 6),
            'stove_type_id' => $this->faker->numberBetween(1, 3),
            'heat_type_id' => $this->faker->numberBetween(1, 3),
            'temperature' => $this->faker->numberBetween(30, 150),
            'capacity' => $this->faker->numberBetween(1, 20),
            'additional_info' => $this->faker->word,
        ];
    }
}
