<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

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

    public function test_genreFilter_getAllBooks(): void
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $response = $this->getJson('/api/books?genre=' . $book1->genre_id);

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
            ->assertInvalid('genre')
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

    public function test_getAllBooks_invalidClaimed(): void
    {
        $response = $this->getJson('/api/books?claimed=2');

        $response->assertStatus(422)
            ->assertInvalid('claimed')
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_getAllBooks_genreAndClaimed(): void
    {
        $book1 = Book::factory(['claimed' => 1])->create();
        $book2 = Book::factory(['claimed' => 1])->create();
        $book3 = Book::factory(['claimed' => 0])->create();
        $response = $this->getJson("/api/books?genre=" . $book1->genre_id . "&claimed=1");

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
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_return_invalidEmail(): void
    {
        $book = Book::factory(['claimed' => 1])->create();

        $response = $this->putJson("/api/books/return/$book->id", ['email' => 'invalid']);

        $response->assertStatus(422)
            ->assertInvalid(['email'])
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'errors']);
            });
    }

    public function test_return_alreadyUnclaim(): void
    {
        $book = Book::factory(['claimed' => 0])->create();

        $response = $this->putJson("/api/books/return/$book->id", ['email' => $book->email]);
        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });
    }

    public function test_return_success(): void
    {
        $book = Book::factory(['claimed' => 1])->create();

        $response = $this->putJson("/api/books/return/$book->id", ['email' => $book->email]);

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
    // ------------------------------------------------------------------------

    public function test_addBook_validData(): void
    {

        $genre = Genre::factory()->create();

        $response = $this->postJson('/api/books/', [
            'title' =>  'hfjdshfsja',
            'author' =>  'hasbulla',
            'genre_id' => $genre->id,
            'blurb' => "test",
            'image' => "url",
            'year' => 2001,
        ]);


        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });

        $this->assertDatabaseHas('books', [
            'title' =>  'hfjdshfsja',
            'author' =>  'hasbulla',
            'genre_id' => $genre->id,
            'blurb' => "test",
            'image' => "url",
            'year' => 2001,
        ]);
    }

    public function test_addBook_validDataRequiredOnly(): void
    {

        $genre = Genre::factory()->create();

        $response = $this->postJson('/api/books/', [
            'title' =>  'hfjdshfsja',
            'author' =>  'hasbulla',
            'genre_id' => $genre->id,
            
        ]);


        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message']);
            });

        $this->assertDatabaseHas('books', [
            'title' =>  'hfjdshfsja',
            'author' =>  'hasbulla',
            'genre_id' => $genre->id,
        ]);
    }

    public function test_addBook_invalidData(): void
    {
        $response = $this->postJson('/api/books/', [
            'title' => 123,
            'author' => 123,
            'genre_id' => 5,
            'blurb' => 123,
            'image' => 5,
            'year' =>  2040,

        ]);

        $response->assertStatus(422)
            ->assertInvalid([
                'title',
                'author',
                'genre_id',
                'blurb',
                'image',
                'year'
            ]);
    }

    public function test_addBook_noData(): void
    {

        $response = $this->postJson('/api/books/', [
            'title' => '',
            'author' => '',
            'genre_id' => '',
        ]);

        $response->assertStatus(422)
            ->assertInvalid([
                'title',
                'author',
                'genre_id'
            ]);
    }
}
