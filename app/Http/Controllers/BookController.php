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
            'data' => Book::with(['genre:id,name'])->get()->makeHidden(['genre_id', 'review_id', 'deleted_at', 'deleted', 'email', 'claimed', 'year', 'page_count', 'claimed_by_name', 'updated_at', 'created_at', 'blurb']),
            'message' => 'Book successfully retrieved',
        ]);
    }

    public function getBookFromId(int $id)
    {
        $book = Book::with(['genre:id,name', 'reviews:id,name,rating,review,book_id'])->find($id);

        if (!$book) {
            return response()->json([
                "message" => "Book with id $id not found"
            ], 404);
        }

        return response()->json([
            'data' => $book,
            'message' => 'Book successfully found'
        ]);
    }
<<<<<<< HEAD
=======

    public function claimBook(int $id, Request $request)
    {

        $bookToUpdate = Book::find($id);

        if ($bookToUpdate) {

            if ($bookToUpdate->claimed == 1) {
                return response()->json([
                    'message' => "Book $id is already claimed",
                ], 400);
            } elseif ($bookToUpdate->claimed == 0) {

                $request->validate([
                    'claimed_by_name' => 'string|min:1|required',
                    'email' => 'string|email|required',
                ]);

                $bookToUpdate->name = $request->name;
                $bookToUpdate->email = $request->email;
                $bookToUpdate->claimed = 1;

                if ($bookToUpdate->save()) {
                    return response()->json([
                        'message' => "Book $id was claimed",
                    ]);
                }
            }
        }

        return response()->json([
            'message' => "Book $id was not found",
        ], 404);
    }
>>>>>>> 2c0075ad12dd03f3f22c00fb5cc90e2352f0cd15
}
