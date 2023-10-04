<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    //
    public function getAllBooks(Request $request)
    {

        $request->validate([
            'claimed' => 'integer|min:0|max:1',
        ]);

        $hidden = ['genre_id',
            'review_id',
            'deleted_at',
            'deleted',
            'email',
            'claimed',
            'year',
            'page_count',
            'claimed_by_name',
            'updated_at',
            'created_at',
            'blurb',
        ];

        $claimed = $request->claimed;
        if ($claimed !== null) {
            $books = Book::with(['genre:id,name'])->where('claimed', $claimed)->get()->makeHidden($hidden);

            return response()->json([
                'data' => $books,
                'message' => 'Book successfully retrieved',
            ]);
        } $books = Book::with(['genre:id,name'])->get()->makeHidden($hidden);

        return response()->json([
            'data' => $books,
            'message' => 'Book successfully retrieved',
        ]);
    }

    public function getBookFromId(int $id)
    {
        $book = Book::with(['genre:id,name', 'reviews:id,name,rating,review,book_id'])->find($id);

        if (! $book) {
            return response()->json([
                'message' => "Book with id $id not found",
            ], 404);
        }

        return response()->json([
            'data' => $book,
            'message' => 'Book successfully found',
        ]);
    }

    public function claimBook(int $id, Request $request)
    {

        $bookToUpdate = Book::find($id);

        if (! $bookToUpdate) {
            return response()->json([
                'message' => "Book $id was not found",
            ], 404);
        }

        if ($bookToUpdate->claimed == 1) {
            return response()->json([
                'message' => "Book $id is already claimed",
            ], 400);
        } elseif ($bookToUpdate->claimed == 0) {

            $request->validate([
                'name' => 'string|min:1|max:255|required',
                'email' => 'string|email|max:255|required',
            ]);

            $bookToUpdate->claimed_by_name = $request->name;
            $bookToUpdate->email = $request->email;
            $bookToUpdate->claimed = 1;

            if ($bookToUpdate->save()) {
                return response()->json([
                    'message' => "Book $id was claimed",
                ]);
            }
        }

    }
}
