<?php

namespace Tests\Feature;

use App\Models\Book;
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

    public function test_claimed_noData(): void
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/claim/$book->id");

        $response->assertStatus(422);
    }

    public function test_claimed_noId(): void
    {
        $response = $this->putJson('/api/books/claim/1');

        $response->assertStatus(404);
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
            "name" => "name",
            "email" => "email@email.com",
            "claimed" => 1
        ]);

        $response->assertStatus(200)
        ->assertJson(function (AssertableJson $json) {
            $json->hasAll([
                'message'
            ]);
        });

        $this->assertDatabaseHas('books', [
            "claimed_by_name" => "name",
            "email" => "email@email.com",
            "claimed" => 1
        ]);

    }
}
