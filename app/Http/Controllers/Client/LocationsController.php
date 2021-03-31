<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Locations as ResourcesLocations;
use App\Locations;

class LocationsController extends Controller
{
    public function searchData (Request $request) {
        if (!empty($request->q)) {
            // $data = Locations::where(
            //     'postcode_sector', 'LIKE', '%'.$request->q.'%',
            // )->paginate(5);
            $data = Locations::where(function ($query) use ($request) {
                $query->where('postcode_sector', 'LIKE', '%' . $request->q . '%')
                      ->orWhere('local_government_area', 'LIKE', '%' . $request->q . '%')
                      ->orWhere('name', 'LIKE', '%' . $request->q . '%');
            })->paginate(5);
            
            return new ResourcesLocations($data);
        } else {
            return response()->json([
                'error' => 'No Result Found.'
            ], 500);
        }
    }

    public function getSingleLocation ($id) {
        try {
            $data = Locations::where('id', $id)->first();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No Location Found.'
            ], 500);
        }
    }
}
