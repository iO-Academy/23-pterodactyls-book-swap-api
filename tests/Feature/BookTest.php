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

    public function test_blankSearch_getAllBooks(): void
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

    public function test_getAllGenreBooks(): void
    {
        $book = Book::factory()->create();
        $response = $this->getJson('/api/books?genre='.$book->genre_id);

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

    public function test_getAllBooks_invalidGenre(): void
    {
        $response = $this->getJson('/api/books?genre=5');

        $response->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
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

    public function test_getAllBooks_genreAndClaimed(): void
    {
        Book::factory()->create();
        Book::factory(['genre_id' => 1, 'claimed' => 1])->create();
        $response = $this->getJson('/api/books?genre=1&claimed=1');

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

    public function test_return_noEmail(): void
    {
        $book = Book::factory(['claimed' => 1])->create();

        $response = $this->putJson("/api/books/return/$book->id");

        $response->assertStatus(422)
            ->assertInvalid(['email'])
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_return_alreadyUnclaim(): void
    {
        $book = Book::factory(['claimed' => 0])->create();

        $response = $this->putJson("/api/books/return/$book->id");
        $response->assertStatus(400);
    }

    public function test_return_success(): void
    {
        $book = Book::factory(['claimed' => 1])->create();

        $response = $this->putJson("/api/books/return/$book->id?email=$book->email");

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll([
                    'message',
                ]);
            });

        $this->assertDatabaseHas('books', [
            'claimed' => 0,
        ]);
    }
}
