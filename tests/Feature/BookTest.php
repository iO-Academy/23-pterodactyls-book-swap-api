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
                            ->has('reviews', function (AssertableJson $json) {
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

    // public function test_failure_getBookFromId(): void
    // {
    // }
}
