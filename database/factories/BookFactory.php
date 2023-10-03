<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\Review;
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
            'page_count' => $this->faker->numberBetween(1, 1000),
            'year' => $this->faker->year(),
            'blurb' => $this->faker->text(),
            'author' => $this->faker->name(),
            'image' => $this->faker->imageUrl(),
            'genre_id' => Genre::factory(),
            'claimed' => 0,
            'claimed_by_name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
    }
}
