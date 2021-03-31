<?php

namespace App\Http\Controllers;

use App\Resellers;
use App\Http\Resources\Resellers as ResellersResource;
use Illuminate\Http\Request;
use DB;

class ResellersController extends Controller
{
    public function index()
    {
        try {
            $resellers = Resellers::paginate(20);

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
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
            $request->solution_id = implode(',' ,$request->solution_id);
            $resellers = new Resellers($request->except('review', 'solutions'));
            $resellers->save();
            
            $ids = [];

            foreach ($request->solutions as $solution) {
                array_push($ids, $solution['id']);
            }

            $resellers->solutions()->sync($ids);

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
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
            $resellers = Resellers::where('id', $id)->with('solutions')->first();
            if($resellers->solution_id){
                $resellers->solution_id = explode (",", $resellers->solution_id); 
            }

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
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
            
            DB::enableQueryLog();
            $resellers = Resellers::where('id', $id)->first();
            $x = 1;
            $sol = '';
            foreach($request->solution_id as $data){
                $sol .= $data;
                if(count($request->solution_id) > $x){
                    $sol .= ',';
                }
                $x += 1;
            }

            if(isset($sol) && $sol != ''){
                $resellers->solution_id = $sol;
            }
            
            
            $resellers->update($request->except('review', 'solutions'));
            $query = DB::getQueryLog();
            $ids = [];

            foreach ($request->solutions as $solution) {
                array_push($ids, $solution['id']);
            }

            $resellers->solutions()->sync($ids);

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $resellers = Resellers::where('id', $id)->first();
            $resellers->delete();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Resellers::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
                });

                return response()->json('Successfully deleted resellers.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        try { 
            if (!empty($request->q)) {
                $data = Resellers::where(
                    'name', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
