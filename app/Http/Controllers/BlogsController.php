<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Blog as BlogResource;
use App\Http\Resources\BlogCollection;
use App\Blog;

class BlogsController extends Controller
{
    // - Done
    public function index()
    {
        try {
            $locations = Blog::orderBy('updated_at', 'DESC')->paginate(30);
            return response()->json($locations, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        $post = new Blog($request->all());
        $post->save();
        
        return new BlogResource($post);
    }

    public function show($id)
    {
        return new BlogResource(Blog::where('id', $id)->first());
    }

    public function update(Request $request, $url)
    {
        $post = Blog::where('id', $url)->first();
        $post->update($request->all());
        
        return new BlogResource($post);
    }

    public function destroy($id)
    {
        $post = Blog::findOrFail($id);
        $post->delete();
        
        return new BlogResource($post);
    }


    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Blog::find($request->ids)->each(function ($blog, $key) {
                    $blog->delete();
                });

                return response()->json('Successfully deleted categories.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        try { 
            if (!empty($request->q)) {
                $data = Blog::where('title', 'LIKE', '%'.$request->q.'%')->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
