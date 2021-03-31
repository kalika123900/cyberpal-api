<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Projects as ProjectsResource;
use App\Http\Resources\ProjectsCollection;
use App\Merchants;
use App\Notifications\CustomerExpertAvailable;
use App\Projects;
use App\User;
use App\Notifications\MerchantProjectInvite;
use App\Notifications\CustomerProjectCompleted;
use App\Notifications\MerchantProjectCompletedAccepted;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectsExport;

class ProjectsController extends Controller
{
    // - Done
    public function index(Request $request)
    {
        try {
            if (!empty($request->status)) {
                $projects = Projects::where('status', $request->status)->with('user')->orderBy('created_at', "DESC")->paginate(30);

                return response()->json($projects, 200);
            } else return response()->json('Operation not found', 404);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $post = new Projects($request->all());
            $post->save();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
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
            $resellers = Projects::where('id', $id)->with('user')->first();

            return response()->json([
                'message' => 'Successfully fetched reviews.',
                'data' => $resellers
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }
    public function updateData (Request $request, $id) {
        try {
            $project = Projects::findOrFail($id);
            $project->update($request->all());

            return response()->json($project, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 404);
        }
    }
    // - Done
    public function update(Request $request, $id)
    {
        if (!empty($request->status) && $id) {
            try {
                $project = Projects::findOrFail($id);
                $project->status = $request->status;
                $project->update();

                if ($project->status === "completed") {
                    $user = User::where('id', $project->user)->first();
                    if (!empty($user)) {
                        $user->notify(new CustomerProjectCompleted($user, $project->reference_id));
                    }

                    $merchant = User::where('id', $project->merchant_id)->first();
                    if (!empty($merchant)) {
                        $merchant->notify(new MerchantProjectCompletedAccepted($user, $project->reference_id));
                    }
                }

                return response()->json($project, 200);
            } catch (\Exception $err) {
                return response()->json($err->getMessage(), 404);
            }
        } else return response()->json('Operation not found.', 404);
    }

    public function delete($id)
    {
        try {
            $post = Projects::findOrFail($id);
            $post->delete();

            return response()->json([
                'message' => 'Successfully deleted projects.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function inviteMerchantsToAProject ($id, Request $request) {
        try {
            $project = Projects::where('id', $id)->first();

            if ($project->status === 'processing' || $project->status === "failed" || $project->status === "completed") {
                $project->status = 'reviewing';
                $project->update();
            }

            // if ($project->status === "active") {
                $project->invitedMerchants()->sync($request->merchantIds);
                $users = $project->invitedMerchants;
                
                $experts = '';
                $count = 0;

                foreach ($users as $user) {
                    $user->notify(new MerchantProjectInvite($user));

                    if ($experts !== "") {
                        $experts = $experts . ', ' . $user->name;
                    } else {
                        $experts = $user->name;
                    }
                    $count = $count + 1;
                }

                $client = $project->user;
                $client->notify(new CustomerExpertAvailable($client, $experts, $count));
                
                return response()->json([
                    'message' => 'Merchants invited to the project.',
                    'data' => $project
                ], 200);
            // } else abort(500, 'Active projects can\'t be updated');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAssignedAgents ($id) {
        try {
            $project = Projects::where('id', $id)->first();
            $data = $project->invitedMerchants;
                
            return response()->json([
                'message' => 'All merchants assigned to single request.',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);        
        }
    }

    public function getSingleMerchantProposals ($id, Request $request) {
        try {

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);        
        }
    }

    // - Done
    public function deleteMultipleData(Request $request) {
        try {
            if ($request->ids) {
                Projects::find($request->ids)->each(function ($location, $key) {
                    $location->delete();
                });

                return response()->json('Successfully deleted pages.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        try { 
            if (!empty($request->q) && !empty($request->status)) {
                $data = Projects::where(function ($query) use ($request) {
                    $query->where('reference_id', 'LIKE', '%' . $request->q . '%')
                        ->orWhere('business_name', 'LIKE', '%' . $request->q . '%');
                })->where(
                    'status', $request->status
                )->orderBy(
                    'created_at', 'DESC'
                )->paginate(30);

                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function searchMerchantData (Request $request) {
        try { 
            if (!empty($request->q)) {
                if (!empty($request->role)) {
                    $data = User::where(
                        'name', 'LIKE', '%'.$request->q.'%'
                    )->whereHas('merchant', function ($q) use ($request) {
                        $q->where('vendor_type', $request->role);
                    })->with('merchant')->orderBy(
                        'created_at', 'DESC'
                    )->limit(20)->get();

                    return response()->json($data, 200);
                } else {
                    // - Other Solutions
                    $data = User::whereHas('merchant', function ($q) use ($request) {
                        $q->where('vendor_type', '!=' ,'expert');
                        $q->where('business_name', 'LIKE', '%'.$request->q.'%');
                    })->with('merchant')->orderBy(
                        'created_at', 'DESC'
                    )->limit(20)->get();

                    return response()-> json($data, 200);   
                }
            } else return response()->json('Operation Not permitted', 400);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function export (Request $request) {
        if ($request->has('format')) {
            if ($request->format === "excel") { 
                return Excel::download(new ProjectsExport, 'projects-collection.xlsx');
            } 

            if ($request->format === "csv") {
                return Excel::download(new ProjectsExport, 'projects-collection.csv');
            }
        } 

        return response()->json('Operation not permitted.', 400);
    }
}
