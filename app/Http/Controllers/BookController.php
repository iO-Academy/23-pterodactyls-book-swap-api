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
        $book = Book::with(['genre:id,name', 'reviews:id,name,rating,review,book_id'])->find($id);

        if (!$book) {
            return response()->json([
                "message" => "Book with id $id not found"
            ]);
        }

        return response()->json([
            'data' => $book,
            'message' => 'Book successfully retrieved'
        ]);
    }
}
