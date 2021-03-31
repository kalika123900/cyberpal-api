<?php
namespace App\Http\Controllers;

use App\CyberPalReviews;
use Illuminate\Http\Request;
use App\Http\Resources\CompanyCollection;
use App\Solution;
use App\Feature;
use App\ResellerSolution;
use App\FeatureSolution;
use App\Company;
use DB;

class CompanyController extends Controller
{

  public function index (Request $request)
  {        
      $data = [];
      $condition = [];
      $paginate = 50;
      
      if ($request->has('paginate')) $paginate = $request->query('paginate');

      try {
          $data = Company::where($condition)->orderBy('updated_at', 'DESC')->paginate($paginate);
          
          return new CompanyCollection($data, 200);
      } catch (\Exception $err) {
          return response()->json([
              'message' => $err->getMessage()
          ], 400);
      }
  }
  public function show($id)
  {
    try {
            $company = Company::where('id', $id)->first();

            return response()->json([
                'message' => 'Successfully fetched solution.',
                'data' => $company
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
      $post = new Company();
      $post->company_name = $request->company_name;
      $post->email    = $request->email;
      $post->password = bcrypt($post->password);
      $post->save();
        return response()->json([
              'message' => 'Successfully stored Company.',
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
            
            $post = Company::where('id', $id)->first();
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
            $data = Company::where('id', $id)->first();
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
                Company::find($request->ids)->each(function ($blog, $key) {
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
                $data = Company::where('company_name', 'LIKE', '%'.$request->q.'%')->where($condition)->paginate($paginate);
                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}