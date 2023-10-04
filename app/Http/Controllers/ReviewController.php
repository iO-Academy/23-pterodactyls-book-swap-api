<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function addReview(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:255',
            'rating' => 'required|integer|min:0|max:5',
            'review' => 'required|string|min:3|max:500',
            'book_id' => 'required|integer|exists:books,id'
        ]);

        $newReview = new Review();
        $newReview->name = $request->name;
        $newReview->rating = $request->rating;
        $newReview->review = $request->review;
        $newReview->book_id = $request->book_id;

        if($newReview->save()) {
            return response()->json([
                'message' => 'Review created'
            ],201);
        }
        return response()->json([
            'message' => 'Unexpected error occurred'
        ], 500);

    }
}
