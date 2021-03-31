<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Event as EventResource;
use App\Http\Resources\EventCollection;
use App\Event;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::orderBy('updated_at', 'DESC')->paginate(30);

            return new EventCollection($events, 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        $event = new Event($request->all());
        $event->save();
        
        return new EventResource($event);
    }

    public function show($id)
    {
        return new EventResource(Event::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());
        
        return new EventResource($event);
    }

    public function destroy($id)
    {
        abort(404);
    }

    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Event::find($request->ids)->each(function ($blog) {
                    $blog->delete();
                });

                return response()->json([
                    'message' => 'Successfully deleted categories.'
                ], 200);
            } else response()->json([
                'message' => 'Operation Not permitted'
            ], 400); 
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function searchData(Request $request) {
        try { 
            if (!empty($request->q)) {
                $data = Event::where(
                    'title', 'LIKE', '%'.$request->q.'%'
                )->paginate(30);

                return new EventCollection($data);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
