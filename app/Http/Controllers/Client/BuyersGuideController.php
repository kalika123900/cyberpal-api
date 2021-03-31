<?php

namespace App\Http\Controllers\Client;

use App\BuyersGuide;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuyersGuide as ResourcesBuyersGuide;
use App\Http\Resources\BuyersGuideCollection;
use Illuminate\Http\Request;

class BuyersGuideController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];

        if(!empty($request->paginate)) {
            $data = BuyersGuide::where('isPublished', 1)->orderBy('created_at', 'ASC')->paginate($request->paginate);
        } else {
            $data = BuyersGuide::where('isPublished', 1)->orderBy('created_at', 'ASC')->paginate(12);
        }
        
        return new BuyersGuideCollection($data);
    }

    public function searchData (Request $request) {
        if (!empty($request->q)) {
            $data = BuyersGuide::where(
                'title', 'LIKE', '%'.$request->q.'%'
            )->where(
                'isPublished', 1
            )->paginate(15);
            
            return new ResourcesBuyersGuide($data);
        } else {
            return response()->json([
                'error' => 'No Result Found.'
            ], 500);
        }
    }

    public function downloadSolutionsPDF () {
        $file = public_path() . "/solutions-buyers-guide.pdf";
        return response()->download($file);
    }
}
