<?php

namespace App\Http\Controllers\Client;

use App\Categories;
use App\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course as ResourcesCourse;
use App\Http\Resources\CourseCollection;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function getAllData (Request $request)
    {
        $data = [];
        $condition = ['isPublished' => 1];
        $paginate = 20;
        
        if ($request->has('vendor') && $request->vendor !== 'all') $condition = array_merge($condition, [
            'vendor' => $request->query('vendor')
        ]);

        if ($request->has('expertise') && $request->expertise !== 'all') $condition = array_merge($condition, [
            'expertize_level' => $request->query('expertise')
        ]);

        if ($request->has('language') && $request->language !== 'all') $condition = array_merge($condition, [
            'language' => $request->query('language')
        ]);

        if ($request->has('is_certification_provided') && $request->is_certification_provided === '1') $condition = array_merge($condition, [
            'is_certification_provided' => 1
        ]);

        if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
            'category_id' => $request->query('category')
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        $data = Course::where($condition)->orderBy('created_at', 'ASC')->paginate($paginate);
        
        return new CourseCollection($data);
    }

    public function getSingleData ($url) {
        $data = Course::where([
            'url' => $url,
            'isPublished' => 1
        ])->first();

        if (empty($data->id)) {
            return response()->json([
                'error' => 'Course doesn\'t exist.'
            ], 500);
        }

        return new ResourcesCourse($data);
    }

    public function getAllCategoryCourses ($category_url) {
        $category = Categories::where('url', $category_url)->first();

        if (empty($category->id)) {
            return response()->json([
                'error' => 'Category doesn\'t exist.'
            ], 500);
        }

        $data = Course::where([
            'category_id' => $category->id,
            'isPublished' => 1
        ])->orderBy('created_at', 'ASC')->paginate(12);
        
        return new CourseCollection($data);
    }

    public function searchData (Request $request) {
        if (!empty($request->q)) {
            $data = Course::where('title', 'LIKE', '%'.$request->q.'%')->where('isPublished', 1)->paginate(15);
            
            return new ResourcesCourse($data);
        } else {
            return response()->json([
                'error' => 'No Result Found.'
            ], 500);
        }
    }

    public function getFiltersData () {
        $vendors = Course::where('isPublished', 1)->groupBy('vendor')->pluck('vendor')->toArray();
        $languages = Course::where('isPublished', 1)->groupBy('language')->pluck('language')->toArray();
        $prices = Course::where('isPublished', 1)->groupBy('price')->pluck('price')->toArray();

        return response()->json([
            'vendors' => $vendors,
            'languages' => $languages,
            'prices' => $prices
        ], 200);
    }
}
