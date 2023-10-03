<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    //
    public function getAllBooks()
    {
        return response()->json([
            'data' => Book::with(['genre:id,name'])->get()->makeHidden(['genre_id', 'review_id', 'deleted_at', 'deleted', 'email', 'name', 'claimed', 'year', 'page_count', 'claimed_by_name', 'updated_at', 'created_at', 'blurb']),
            'message' => 'Book successfully retrieved'
        ]);
    }

    public function getBookFromId(int $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                
                "message" => "The claimed field must be a number. (and 2 more errors)",
                "errors" =>[ 
                    "claimed" => "The claimed field must be a number.",
                    "genre" => "The selected genre is invalid.",
                    "search" => "The search field must be a string." 
                ]
            ]);
        }

        return response()->json([
            'data' => Book::with(['genre:id,name'])->get()->makeHidden(['genre_id', 'review_id', 'deleted_at', 'deleted', 'email', 'name', 'claimed', 'year', 'page_count', 'claimed_by_name', 'updated_at', 'created_at', 'blurb']),
            'message' => 'Book successfully retrieved'
        ]);
    }
}
