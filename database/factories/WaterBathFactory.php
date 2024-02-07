<?php

namespace Database\Factories;

use App\Models\WaterBath;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Generator as Faker;


class WaterBathFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WaterBath::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sauna_id' => $this->faker->numberBetween(6, 6),
            'bath_type_id' => $this->faker->numberBetween(1, 3),
            'water_type_id' => $this->faker->numberBetween(1, 6),
            'temperature' => $this->faker->numberBetween(30, 150),
            'capacity' => $this->faker->numberBetween(1, 20),
            'deep_water' => $this->faker->text,
            'additional_info' => $this->faker->word,
        ];
    }
}
