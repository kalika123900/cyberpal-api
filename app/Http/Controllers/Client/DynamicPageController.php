<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DynamicSolutionsPage;

class DynamicPageController extends Controller
{
      public function getHomepageData (Request $request) {
        if (!empty($request->type)) {
            try {
                $solutions = DynamicSolutionsPage::where('type', $request->type)->with('solution')->get();
                return response()->json($solutions, 200);
            } catch (\Exception $err) {
                return response()->json([
                    'error' => $err
                ], 500);
            }
        } else abort(404);
    }
}
