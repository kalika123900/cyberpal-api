<?php
namespace App\Http\Controllers;

use App\CyberPalReviews;
use Illuminate\Http\Request;
use App\Http\Resources\FeatureCollection;
use App\Solution;
use App\Feature;
use App\ResellerSolution;
use App\FeatureSolution;
use App\Company;
use DB;

class FeatureController extends Controller
{

  public function index (Request $request)
  {        
      $data = [];
      $condition = [];
      $paginate = 50;
      
      if ($request->has('paginate')) $paginate = $request->query('paginate');

      try {
          $data = Feature::where($condition)->select('feature_master.id','feature_master.feature_name', 'feature_master.type', 'feature_master.updated_at','name')->join('categories', 'categories.id', '=', 'feature_master.category')->orderBy('feature_master.updated_at', 'DESC')->paginate($paginate);

         
        //   return $data;
         return new FeatureCollection($data, 200);
      } catch (\Exception $err) {
          return response()->json([
              'message' => $err->getMessage()
          ], 400);
      }
  }
  public function show($id)
  {
    try {
            $feature = Feature::where('feature_master.id', $id)->select('feature_master.id','feature_master.feature_name', 'feature_master.type', 'feature_master.updated_at','name', 'categories.id as category_id')->join('categories', 'categories.id', '=', 'feature_master.category')->first();

            return response()->json([
                'message' => 'Successfully fetched solution.',
                'data' => $feature
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }
  public function store (Request $request)
  {        
      try {
      $post = new Feature();
      $post->feature_name = $request->feature_name;
      $post->type    = $request->type;
      $post->category = $request->category;
      $post->save();
        return response()->json([
              'message' => 'Successfully stored Feature.',
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
        $data = $request->all();
        
        try {
            
            $post = Feature::where('id', $id)->first();
            $post->update($data);
            
            return response()->json([
                'message' => 'Successfully updated Company.',
                'data' => $post
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
            $data = Feature::where('id', $id)->first();
            $data->delete();

            return response()->json([
                'message' => 'Successfully Deleted Company.',
                'data' => $data
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }
   
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Feature::find($request->ids)->each(function ($blog, $key) {
                    $blog->delete();
                });
                return response()->json('Successfully deleted company.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }
    public function searchData(Request $request) {
        $data = [];
        $condition = [];
        $paginate = 12;
        
        if ($request->has('tab') && $request->tab !== 'all') $condition = array_merge($condition, [
            'organisation_size' => $request->tab
        ]);

        if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
            'category_id' => $request->category
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try { 
            if (!empty($request->q)) {
                $data = Feature::where('feature_name', 'LIKE', '%'.$request->q.'%')->where($condition)->select('feature_master.id','feature_master.feature_name', 'feature_master.type', 'feature_master.created_at','name')->join('categories', 'categories.id', '=', 'feature_master.category')->paginate($paginate);
                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}