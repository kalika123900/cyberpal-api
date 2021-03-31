<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pages;

class PagesController extends Controller
{
    // - Done
    public function index()
    {
        try {
            $pages = Pages::orderBy('updated_at', 'DESC')->paginate(30);

            return response()->json($pages, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $post = new Pages($request->all());
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
            $pages = Pages::where('id', $id)->first();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $pages
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
            $post = Pages::where('id', $id)->first();
            $post->update($request->all());

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

    public function destroy($id)
    {
        try {
            $post = Pages::findOrFail($id);
            $post->delete();
            
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
    
    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Pages::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
                });

                return response()->json('Successfully deleted pages.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        try { 
            if (!empty($request->q)) {
                $data = Pages::where(
                    'title', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
