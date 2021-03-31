<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Locations;

// - Validations
// - Admin Validations
class LocationsController extends Controller
{
    // - Done
    public function index()
    {
        try {
            $locations = Locations::paginate(30);
            return response()->json($locations, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $post = new Locations($request->all());
            $post->save();
            
            return response()->json([
                'message' => 'Successfully added new Location.',
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
            $location = Locations::where('id', $id)->first();
                        
            return response()->json([
                'message' => 'Successfully loaded Location.',
                'data' => $location
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
            $location = Locations::where('id', $id)->update($request->all()); 

            return response()->json([
                'message' => 'Successfully updated Location.',
                'data' => $location
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    // - Done
    public function destroy()
    {
        return response()->json('Operation not found', 404);
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Locations::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
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
                $data = Locations::where(function ($query) use ($request) {
                    $query->where('postcode_sector', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('local_government_area', 'LIKE', '%' . $request->q . '%');
                })->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
