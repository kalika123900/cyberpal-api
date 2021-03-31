<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TopSolutions;
use App\Solution;

class TopSolutionsController extends Controller
{
    // - Done
    public function index()
    {
        try {
            $data = TopSolutions::paginate(30);
            return response()->json($data, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = new TopSolutions($request->except('solutions'));
            $data->save();
            
            return response()->json([
                'message' => 'Successfully added new solution.',
                'data' => $data
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
            $data = TopSolutions::where('id', $id)->first();

            if($data->solution_id != 0){
                $data->solutions = Solution::where('id', $data->solution_id)->first();
            }else{
                $data->solutions = [];
            }
                        
            return response()->json([
                'message' => 'Successfully loaded top solutions.',
                'data' => $data
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
           
            $data = TopSolutions::where('id', $id)->update($request->except('solutions')); 

            return response()->json([
                'message' => 'Successfully updated top solutions.',
                'data' => $data
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    // - Done
    public function destroy()
    {
        return response()->json('Operation not found', 404);
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                TopSolutions::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
                });

                return response()->json('Successfully deleted top solutions.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }
}
