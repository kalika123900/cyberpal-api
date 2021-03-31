<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\BuyersGuide as BuyersGuideResource;
use App\Http\Resources\BuyersGuideCollection;
use App\BuyersGuide;

class BuyersGuideController extends Controller
{
    // - Done
    public function index()
    {
        try {
            $faqs = BuyersGuide::orderBy('updated_at', 'DESC')->paginate(30);
            return response()->json($faqs, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // - I am not doing validation here
        $post = new BuyersGuide($request->all());
        $post->save();
        
        return new BuyersGuideResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new BuyersGuideResource(BuyersGuide::where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //I am doing validation here
        $post = BuyersGuide::where('id', $id)->first();
        $post->update($request->all());
        
        return new BuyersGuideResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = BuyersGuide::findOrFail($id);
        $post->delete();
        
        return new BuyersGuideResource($post);
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                BuyersGuide::find($request->ids)->each(function ($blog, $key) {
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
                $data = BuyersGuide::where(
                    'title', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
