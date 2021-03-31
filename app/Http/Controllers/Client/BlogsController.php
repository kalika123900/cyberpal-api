<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Blog;
use App\Http\Resources\Blog as ResourcesBlog;
use App\Http\Resources\BlogCollection;

class BlogsController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];

        if(!empty($request->paginate)) {
            $data = Blog::where('isPublished', 1)->orderBy('created_at', 'ASC')->paginate($request->paginate);
        } else {
            $data = Blog::where('isPublished', 1)->orderBy('created_at', 'ASC')->paginate(12);
        }
        
        return new BlogCollection($data);
    }
        
    public function getSingleData ($url) {
        $data = Blog::where([
            'url' => $url,
            'isPublished' => 1
        ])->first();


        if (empty($data->id)) {
            return response()->json(['error' => 'Blog doesn\'t exist.'], 500);
        }

        $similarBlogs = Blog::where([
            'isPublished' => 1,
        ])->where('url', '!=', $url)->inRandomOrder()->limit(3)->get();

        return response()->json([
            'data' => $data,
            'similarBlogs' => $similarBlogs,
        ], 200);
    }

    public function searchData (Request $request) {
        if (!empty($request->q)) {
            $data = Blog::where('title', 'LIKE', '%'.$request->q.'%')->where('isPublished', 1)->paginate(15);

            return new ResourcesBlog($data);
        } else {
            return response()->json([
                'error' => 'No Result Found.'
            ], 500);
        }
    }
}
