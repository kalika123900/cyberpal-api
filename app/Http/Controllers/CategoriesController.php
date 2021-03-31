<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categories;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $paginate = 100;
        $condition = [];

        if ($request->has('type') && $request->type !== 'all') {
            if ($request->type === 'solutions') {
                $condition = array_merge($condition, [
                    'is_in_solutions' => 1
                ]);
            }

            if ($request->type === 'events') {
                $condition = array_merge($condition, [
                    'is_in_events' => 1
                ]);
            }

            if ($request->type === 'certifications') {
                $condition = array_merge($condition, [
                    'is_in_certifications' => 1
                ]);
            }

            if ($request->type === 'experts') {
                $condition = array_merge($condition, [
                    'is_in_experts' => 1
                ]);
            }

            if ($request->type === 'community') {
                $condition = array_merge($condition, [
                    'is_in_community' => 1
                ]);
            }
        }
        
        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try {
            $categories = Categories::where($condition)->orderBy('created_at', 'DESC')->paginate($paginate);

            return response()->json($categories, 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $categories = new Categories($request->all());
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
            $categories = Categories::where('id', $id)->first();

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
            $categories = Categories::findOrFail($id);
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
            $categories = Categories::findOrFail($id);
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
            Categories::find($request->ids)->each(function ($product, $key) {
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
            $data = Categories::where(
                'name', 'LIKE', '%'.$request->q.'%'
            )->paginate($paginate);

            return response()->json($data, 200);
        } else {
            $data = Categories::paginate($paginate);

            return response()->json($data, 200); 
        }
    }
}
