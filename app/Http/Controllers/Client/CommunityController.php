<?php

namespace App\Http\Controllers\Client;

use App\CommunityAnswers;
use App\CommunityQuestions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function getAllData (Request $request)
    {
        $data = [];
        $condition = ['isApproved' => 1];
        $paginate = 20;

        try {
            
            if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
                'category_id' => $request->query('category')
            ]);

            if ($request->has('paginate')) $paginate = $request->query('paginate');

            // if ($request->has('type') && $request->type === 'user') $condition = array_merge($condition, [
            //     'user_id' => auth()->user()
            // ]);

            if ($request->has('type') && $request->type !== 'all') {
                if ($request->type === 'unanswered') {
                    $data = CommunityQuestions::where($condition)->has('answers','=', 0)->with([
                        'category', 
                        'answers' => function ($query) {
                            $query->where('isAccepted', 1);
                        }
                    ])->withCount('answers')->orderBy('created_at', 'ASC')->paginate($paginate);
                    
                    return response()->json($data, 200);
                }
            }

            $data = CommunityQuestions::where($condition)->with([
                'category', 
                'answers' => function ($query) {
                    $query->where('isAccepted', 1);
                }
            ])->withCount('answers')->orderBy('created_at', 'ASC')->paginate($paginate);
            
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addNewQuestion (Request $request) {
        try {
            $url = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
            $request->request->add(['url' => $url]);
            $data = CommunityQuestions::create($request->all());        
            
            return response()->json([
                'data' => $data,
                'message' => 'Question Added'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addNewAnswer ($url, Request $request) {
        try {
            $data = CommunityAnswers::create($request->all());        
            
            return response()->json([
                'data' => $data,
                'message' => 'Question Added'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSingleQuestion ($url) {
        try {
            $data = CommunityQuestions::where('url', $url)->with([
                'answers' => function ($query) {
                    $query->orderBy('isAccepted', 'DESC');
                }
            ])->first();        
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
