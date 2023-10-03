<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

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

    public function claimBook(int $id, Request $request)
    {

        $bookToUpdate = Book::find($id);

        if ($bookToUpdate) {
            if ($bookToUpdate->claimed == 0) {
                $request->validate([
                    'name' => 'string|min:1',
                    'email' => 'string|email',
                ]);

                $bookToUpdate->name = $request->name;
                $bookToUpdate->email = $request->email;
                $bookToUpdate->claimed = 1;

                if ($bookToUpdate->save()) {
                    return response()->json([
                        'message' => "Book $id was claimed",
                    ]);
                }
            } elseif ($bookToUpdate->claimed == 1) {
                return response()->json([
                    'message' => "Book $id is already claimed",
                ]);
            }
        }

        return response()->json([
            'message' => "Book $id was not found",
        ]);

    }
}
