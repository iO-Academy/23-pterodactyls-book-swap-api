<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAllBooks(): void
    {

        Book::factory()->create();
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data', 'message'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll([
                            'id',
                            'title',
                            'author',
                            'image',
                            'genre',
                        ])
                            ->whereAllType([
                                'id' => 'integer',
                                'title' => 'string',
                                'author' => 'string',
                                'image' => 'string',
                            ])
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll([
                                    'id',
                                    'name',
                                ])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            });
                    });
            });
    }

    public function test_getAllClaimedBooks(): void
    {
        Book::factory()->create();
        Book::factory(['claimed' => 1])->create();
        $response = $this->getJson('/api/books?claimed=1');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data', 'message'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll([
                            'id',
                            'title',
                            'author',
                            'image',
                            'genre',
                        ])
                            ->whereAllType([
                                'id' => 'integer',
                                'title' => 'string',
                                'author' => 'string',
                                'image' => 'string',
                            ])
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll([
                                    'id',
                                    'name',
                                ])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            });
                    });
            });
    }

    public function test_getAllBooks_invalidData(): void
    {
        $response = $this->getJson('/api/books?claimed=2');

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_success_getBookFromId(): void
    {
        Review::factory()->create();
        $response = $this->getJson('/api/books/1');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['data', 'message'])
                    ->has('data', function (AssertableJson $json) {
                        $json->hasAll([
                            'id',
                            'title',
                            'author',
                            'blurb',
                            'claimed_by_name',
                            'image',
                            'page_count',
                            'year',
                            'genre',
                            'reviews',
                        ])
                            ->whereAllType([
                                'id' => 'integer',
                                'title' => 'string',
                                'author' => 'string',
                                'image' => 'string',
                                'blurb' => 'string',
                                'claimed_by_name' => 'string',
                                'page_count' => 'integer',
                                'year' => 'integer',
                            ])
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll([
                                    'id',
                                    'name',
                                ])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                    ]);
                            })
                            ->has('reviews', 1, function (AssertableJson $json) {
                                $json->hasAll([
                                    'id',
                                    'name',
                                    'rating',
                                    'review',
                                ])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string',
                                        'rating' => 'integer',
                                        'review' => 'string',
                                    ]);
                            });
                    });
            });
    }

    public function test_failure_getBookFromId(): void
    {
        $response = $this->getJson('/api/books/100');

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message'])
                    ->whereContains('message', 'Book with id 100 not found');
            });
    }

    public function test_claimed_noData(): void
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/claim/$book->id");

        $response->assertStatus(422)
            ->assertInvalid(['email', 'name'])
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_claimed_noId(): void
    {
        $response = $this->putJson('/api/books/claim/1');

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });
    }

    public function test_claimed_alreadyClaimed(): void
    {
        $book = Book::factory(['claimed' => 1])->create();

        $response = $this->putJson("/api/books/claim/$book->id");
        $response->assertStatus(400);

    }

    public function test_claimed_success(): void
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/claim/$book->id", [
            'name' => 'name',
            'email' => 'email@email.com',
            'claimed' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll([
                    'message',
                ]);
            });

        $this->assertDatabaseHas('books', [
            'claimed_by_name' => 'name',
            'email' => 'email@email.com',
            'claimed' => 1,
        ]);

    }

    public function test_addReviews_validData(): void
    {
        $book = Book::factory()->create();

        $response = $this->postJson("/api/books/reviews", [
            'name' => 'hfjdshfsja',
            'rating' => 4,
            'review' => 'great book',
            'book_id' => $book->id
        ]);

        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });

    }

    public function test_addReviews_invalidData(): void
    {

        $response = $this->postJson("/api/books/reviews", [
            'name' => 3242,
            'rating' => 'dog',
            'review' => 'great book',
            'book_id' => 'pizza'
        ]);

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });

    }
}
