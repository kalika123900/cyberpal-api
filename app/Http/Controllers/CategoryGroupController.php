<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CategoryGroup;
use DB;


class CategoryGroupController extends Controller{

  public function index(Request $request){
    $paginate = 100;
    $condition = [];

    if ($request->has('paginate')) $paginate = $request->query('paginate');
    
    try{
        $categories = CategoryGroup::where($condition)->orderBy('created_at', 'DESC')->paginate($paginate);

        return response()->json($categories, 200);
    }catch(\Exception $err){
      return response()->json([
        'status' => 0,
        'data' => null,
        'message' => $err->getMessage()
      ], 400);
    }
  }

   public function store(Request $request)
    {
        try {
            $categories = new CategoryGroup($request->all());
            $categories->save();

            return response()->json([
                'message' => 'Successfully created categories.',
                'data' => $categories
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
            $categories = CategoryGroup::where('id', $id)->first();

            return response()->json([
                'message' => 'Successfully fetched category.',
                'data' => $categories
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
            $categories = CategoryGroup::findOrFail($id);
            $categories->update($request->all());

            return response()->json([
                'message' => 'Successfully updated category.',
                'data' => $categories
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
            $categories = CategoryGroup::findOrFail($id);
            $categories->delete();

            return response()->json([
                'message' => 'Successfully deleted category.',
                'data' => $categories
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function deleteMultipleData(Request $request) {
        try {
            CategoryGroup::find($request->ids)->each(function ($product, $key) {
                $product->delete();
            });

            return response()->json([
                'message' => 'Successfully deleted categories.',
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function searchData (Request $request) {
        $paginate = 12;

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        if (!empty($request->q)) {
            $data = CategoryGroup::where(
                'title', 'LIKE', '%'.$request->q.'%'
            )->paginate($paginate);

            return response()->json($data, 200);
        } else {
            $data = CategoryGroup::paginate($paginate);

            return response()->json($data, 200); 
        }
    }

}

?>