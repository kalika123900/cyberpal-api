<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reviews;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $condition = [];
           
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === "unapproved") {
                $condition = array_merge($condition, [
                    'isApproved' => 0
                ]);
            } else if ($request->status === "approved") {
                $condition = array_merge($condition, [
                    'isApproved' => 1
                ]);
            }
        }
        
        try {
            $reviews = Reviews::where($condition)->with('solution')->paginate(12);

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $reviews
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $post = new Reviews($request->all());
            $post->save();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $post = Reviews::where('id', $id)->first();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $post = Reviews::findOrFail($id);
            $post->update($request->all());

            return response()->json([
                'message' => 'Successfully updated reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy ($id)
    {
        try {
            $post = Reviews::findOrFail($id);
            $post->delete();

            return response()->json([
                'message' => 'Successfully deleted reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function approveReview (Request $request) {
        try {
            $post = Reviews::findOrFail($request->id);
            $post->update([
                'isApproved' => 1
            ]);

            return response()->json([
                'message' => 'Successfully updated reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function unapproveReview (Request $request) {
        try {
            $post = Reviews::findOrFail($request->id);
            $post->update([
                'isApproved' => 0
            ]);

            return response()->json([
                'message' => 'Successfully updated reviews.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }
}
