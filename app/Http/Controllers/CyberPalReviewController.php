<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\CyberPalReview as ReviewResource;
use App\CyberPalReviews;

class CyberPalReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ReviewResource(CyberPalReviews::paginate(12));
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
        $post = new CyberPalReviews($request->all());
        $post->save();
        
        return new ReviewResource($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ReviewResource(CyberPalReviews::where('id', $id)->first());
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
        // - I am doing validation here
        $post = CyberPalReviews::findOrFail($id);
        $post->rating = $request->rating;
        $post->review = $request->review;
        $post->update();
        
        return new ReviewResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = CyberPalReviews::findOrFail($id);
        $post->delete();
        
        return new ReviewResource($post);
    }
}
