<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(20),
            'claimed' => $this->faker->boolean(0),
            'genre_id' => $this->faker->numberBetween(1, 5),
            'page_count' => $this->faker->numberBetween(0, 5000),
            'image' => $this->faker->url(),
            'year' => $this->faker->year(),
            'deleted' => $this->faker->boolean(),
        ];
    }
}
