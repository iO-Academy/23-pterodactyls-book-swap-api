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
}
