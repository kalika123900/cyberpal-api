<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\CourseCollection;
use App\Course;

class CourseController extends Controller
{
    public function index()
    {
       
        try {
            $data = Course::orderBy('updated_at', 'DESC')->paginate(30);

            return new CourseCollection($data, 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        // - I am not doing validation here
        $post = new Course($request->all());
        $post->save();
        
        return new CourseResource($post);
    }

    public function show($id)
    {
        return new CourseResource(Course::where('id', $id)->first());
    }

    public function update(Request $request, $id)
    {
        //I am doing validation here
        $post = Course::findOrFail($id);
        $post->update($request->all());
        
        return new CourseResource($post);
    }

    public function destroy($id)
    {
        $post = Course::findOrFail($id);
        $post->delete();
        
        return new CourseResource($post);
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Course::find($request->ids)->each(function ($blog, $key) {
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
                $data = Course::where(
                    'title', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
