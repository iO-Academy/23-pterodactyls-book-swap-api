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
            'genre' => 'integer|exists:genres,id',
            'search' => 'string|max:500',
        ]);

        $hidden = [
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
        $genre = $request->genre;
        $search = $request->search;

        $books = Book::with(['genre:id,name']);

        if ($claimed) {
            $books = $books->where('claimed', $claimed);
        }

        if ($genre) {
            $books = $books->where('genre_id', $genre);
        }

        if ($search) {
            $books = $books->where('title', 'LIKE', '%'.$search.'%')
                ->orWhere('author', 'LIKE', '%'.$search.'%')
                ->orWhere('blurb', 'LIKE', '%'.$search.'%');
        }

        return response()->json([
            'data' => $books->get()->makeHidden($hidden),
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

    public function returnBook(int $id, Request $request)
    {

        $bookToUpdate = Book::find($id);

        if ($bookToUpdate) {
            if ($bookToUpdate->claimed == 0) {
                return response()->json([
                    'message' => "Book $id is not currently claimed",
                ], 400);
            } elseif ($bookToUpdate->claimed == 1) {

                $request->validate([
                    'email' => 'string|email|max:255|required',
                ]);

                $bookToUpdate->email = '';
                $bookToUpdate->claimed = 0;

                if ($bookToUpdate->save()) {
                    return response()->json([
                        'message' => "Book $id was returned",
                    ]);
                }
            }
        }

        return response()->json([
            'message' => "Book $id was not found",
        ], 404);
    }

    public function addBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:1|max:255',
            'author' => 'required|string|min:1|max:255',
            'genre_id' => 'required|integer|exists:genres,id',
            'blurb' => 'string|min:0|max:500',
            'image' => 'string|min:0|max:225',
            'year' => 'integer|min:0|max:2025',
        ]);

        $newBook = new Book();
        $newBook->title = $request->title;
        $newBook->author = $request->author;
        $newBook->genre_id = $request->genre_id;
        $newBook->blurb = $request->blurb;
        $newBook->image = $request->image;
        $newBook->year = $request->year;

        if ($newBook->save()) {
            return response()->json([
                'message' => 'Review created',
            ], 201);
        }

        return response()->json([
            'message' => 'Unexpected error occured'
        ]);
    }
}
