<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
                        'claimed',
                        'genre_id',
                        'page_count',
                        'image',
                        'year',
                        'deleted'
                    ]);

                });
            });
    }
}
