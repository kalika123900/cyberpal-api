<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reviews as ResourcesReviews;
use App\Http\Resources\ReviewsCollection;
use App\Reviews;
use App\Solution;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];
        $condition = ['isApproved' => 1];
        $paginate = 20;
        
        if ($request->has('solution') && $request->solution !== 'all') $condition = array_merge($condition, [
            'solution' => $request->query('solution_id')
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');
            
        $data = Reviews::where($condition)->orderBy('created_at', 'ASC')->paginate($paginate);
        
        return new ReviewsCollection($data);
    }
    
    public function addNewReview (Request $request) {

        try {
            $data = new Reviews($request->all());
            $data->save();
            
            
            if($data->id){
                return response()->json([
                    'status' => "success",
                    'message' => "Review added successfully"
                ], 200);
            }else{
                return response()->json([
                    'status' => "failed",
                    'message' => "Something went wrong"
                ], 200);
            }
        } catch(\Exception $e){
            return $e->getMessage(); 
        }
    }

    public function getSingleSolutionReviews ($url) {
        $solution = Solution::where('url', $url)->first();

        if (empty($solution)) return response()->json(['error' => 'Solution doesn\'t exist.'], 500);

        $data = Reviews::where([
            'isApproved' => 1,
            'solution_id' => $solution->id
        ])->with('user')->orderBy('created_at', 'ASC')->get();


        return new ReviewsCollection($data);
    }

    public function getAllUserReviews () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $reviews = Reviews::where('user_id', $user->id)->with('solution')->orderBy('updated_at', 'DESC')->paginate(50);

                return response()->json($reviews, 200);
            } else {
                return response()->json([
                    'error' => 'User not found'
                ], 500);
            }

        } catch (\Exception $err) {
            return response()->json([
                'error' => $err->getMessage()
            ], 500);
        } 
    }

    public function getSingleUserReview ($id) {
        try {
            $review = Reviews::where('id', $id)->with('solution')->first();

            return response()->json($review, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);     
        }
    }

    public function editSingleUserReview ($id, Request $request) {
        try {
            Reviews::where('id', $id)->update($request->all());
            return response()->json([
                'message' => 'Review Updated'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);     
        }
    }

    public function deleteSingleUserReview ($id) {
        try {
            Reviews::where('id', $id)->delete();

            return response()->json([
                'message' => 'Review Deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);     
        }
    }
}
