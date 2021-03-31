<?php

namespace App\Http\Controllers\Client;

use App\Categories;
use App\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\Event as ResourcesEvent;
use App\Http\Resources\EventCollection;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];
        $condition = ['isPublished' => 1];
        $paginate = 20;
        
        if ($request->has('vendor') && $request->vendor !== 'all') $condition = array_merge($condition, [
            'vendor' => $request->query('vendor')
        ]);

        if ($request->has('city') && $request->city !== 'all') $condition = array_merge($condition, [
            'city' => $request->query('city')
        ]);

        if ($request->has('start_date') && $request->start_date !== 'all') $condition = array_merge($condition, [
            'start_date' => $request->query('start_date')
        ]);

        if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
            'category_id' => $request->query('category')
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        $data = Event::where($condition)->orderBy('created_at', 'ASC')->paginate($paginate);
        
        return new EventCollection($data);
    }

    public function getSingleData ($url) {
        $data = Event::where([
            'url' => $url,
            'isPublished' => 1
        ])->first();

        if (empty($data->id)) {
            return response()->json(['error' => 'Event doesn\'t exist.'], 500);
        }

        return new ResourcesEvent($data);
    }

    public function getAllCategoryEvents ($category_url) {
        $category = Categories::where('url', $category_url)->first();

        if (empty($category->id)) {
            return response()->json(['error' => 'Category doesn\'t exist.'], 500);
        }

        $data = Event::where([
            'category_id' => $category->id,
            'isPublished' => 1
        ])->orderBy('created_at', 'ASC')->paginate(12);
        
        return new EventCollection($data);
    }

    public function searchData (Request $request) {
        if (!empty($request->q)) {
            $data = Event::where('title', 'LIKE', '%'.$request->q.'%')->where('isPublished', 1)->paginate(15);
            
            return new ResourcesEvent($data);
        } else {
            return response()->json([
                'error' => 'No Result Found.'
            ], 500);
        }
    }

    public function getFiltersData () {
        $vendors = Event::where('isPublished', 1)->groupBy('provided_by')->pluck('provided_by')->toArray();
        $cities = Event::where('isPublished', 1)->groupBy('city')->pluck('city')->toArray();
        $start_dates = Event::where('isPublished', 1)->groupBy('start_date')->pluck('start_date')->toArray();

        return response()->json([
            'vendors' => $vendors,
            'cities' => $cities,
            'start_dates' => $start_dates
        ], 200);
    }
}
