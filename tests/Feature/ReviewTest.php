<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseMigrations;

    public function test_addReviews_validData(): void
    {
        $book = Book::factory()->create();
        $name = 'hfjdshfsja';
        $rating = 4;
        $review = 'great book';

        $response = $this->postJson('/api/reviews', [
            'name' => $name,
            'rating' => $rating,
            'review' => $review,
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });

        $this->assertDatabaseHas('reviews', [
            'name' => $name,
            'rating' => $rating,
            'review' => $review,
            'book_id' => $book->id,
        ]);
    }

    public function test_addReviews_invalidData(): void
    {

        $response = $this->postJson('/api/reviews', [
            'name' => 3242,
            'rating' => 'dog',
            'review' => 543,
            'book_id' => 'pizza',
        ]);

        $response->assertStatus(422)
            ->assertInvalid([
                'name',
                'rating',
                'review',
                'book_id',
            ]);
    }

    public function test_addReviews_noData(): void
    {

        $response = $this->postJson('/api/reviews', [
            'name' => '',
            'rating' => '',
            'review' => '',
            'book_id' => '',
        ]);

        $response->assertStatus(422)
            ->assertInvalid([
                'name',
                'rating',
                'review',
                'book_id',
            ]);
    }
}
