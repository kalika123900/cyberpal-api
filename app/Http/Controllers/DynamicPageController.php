<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DynamicSolutionsPage;

class DynamicPageController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->type) {
                $contacts = DynamicSolutionsPage::where('type', $request->type)->with('solution', 'solution.category')->paginate(30);

                return response()->json($contacts, 200);
            } return response()->json('opernation not permitted', 400);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $post = new DynamicSolutionsPage();
            $post->type = $request->type;
            $post->solution_id = $request->solution_id;
            $post->save();
            
            $data = DynamicSolutionsPage::where('id', $post->id)->with('solution', 'solution.category')->first();

            return response()->json($data, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = DynamicSolutionsPage::where('id', $id)->first();
        return response()->json($post, 200);
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
        $post = DynamicSolutionsPage::findOrFail($id);
        $post->update($request->all());

        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = DynamicSolutionsPage::findOrFail($id);
        $post->delete();
        
        return response()->json($post, 200);
    }
}
