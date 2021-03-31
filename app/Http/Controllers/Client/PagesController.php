<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Page as PageResource;
use App\Pages;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function getSingleData ($url) {
        $data = Pages::where([
            'url' => $url,
            'isPublished' => 1
        ])->first();

        if (empty($data->id)) {
            return response()->json(['error' => 'Page doesn\'t exist.'], 500);
        }

        return new PageResource($data);
    }
}
