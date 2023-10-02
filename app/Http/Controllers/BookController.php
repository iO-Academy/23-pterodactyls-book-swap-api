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
            'data' => Book::all(),
            'message' => 'success'
        ]);
    }
}