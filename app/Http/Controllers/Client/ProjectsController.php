<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreProjectsFormRequest;
use App\Http\Resources\Projects as ProjectsResource;
use App\Projects;
use App\Proposals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cartalyst\Stripe\Stripe;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;
use App\Notifications\ClientAcceptedProposal;
use App\Notifications\CustomerProjectAdded;
use App\Notifications\CustomerProjectPaymentReceived;
use App\Notifications\MerchantProposalAccepted;
use App\Notifications\MerchantProjectPaymentReceived;
use App\User;

class ProjectsController extends Controller
{
    public function addNewdata (StoreProjectsFormRequest $request) {
        try {
            if (Auth::check()) {
                $user = auth()->user();
                
                if (!empty($user)) {
                    do {
                        $refrence_id = mt_rand(1000000000, 9999999999);
                    } while (Projects::where('reference_id', $refrence_id)->exists());
                    
                    $request->merge(['reference_id' => $refrence_id]);
                    $request->merge(['user_id' => $user->id]);
                    
                    $data = new Projects($request->all());
                    $data->save();
                    
                    $loggedInUser = User::where('id', $user->id)->first();
                    $loggedInUser->notify(new CustomerProjectAdded($loggedInUser));

                    return new ProjectsResource($data);
                } else abort(401, 'You need to create an account first');
            } else abort(401, 'You need to login first.');
        } catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllUserRequests (Request $request) {
        try {
            if (Auth::check()) {
                $id = Auth::id();
                
                if (!empty($id)) {
                    $status = ["processing", "reviewing", "active"];

                    if (!empty ($request->status) && $request->status !== "active") {
                       $status = [$request->status];
                    }

                    $data = Projects::where('user_id', $id)->whereIn('status', $status)->with(['invitedMerchants', 'proposals', 'category'])->orderBy('created_at', "DESC")->paginate(30);
                    
                    return response()->json($data, 200);
                 } else abort(401, 'You need to create an account first');
            } else abort(401, 'You need to login first.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSingleUserRequest ($id) {
        try {
            $data = Projects::where('id', $id)->with(['invitedMerchants', 'proposals', 'proposals.merchant' ,'category'])->first();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function activeSingleUserRequest ($id) {
        try {
            $proposal = Proposals::where('id', $id)->first();

            if (empty($proposal) || !$proposal->project_id) {
                return abort(404, "Project proposal not found.");
            }

            $project = Projects::where('id', $proposal->project_id)->first();

            if (!empty($project)) {
                // - Verify Payments
                $project->status = "active";
                $project->update();

                $merchant = $proposal->merchant;
                $merchant->notify(new MerchantProjectPaymentReceived($merchant, $project->reference_id, $proposal->proposed_price));

                $client = $project->user;
                $client->notify(new CustomerProjectPaymentReceived($client, $project->reference_id, $proposal->proposed_price));

                return response()->json($project, 200);
            } else {
                return abort(404, "Project not found");
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateClientStatus ($id, Request $request) {
        try {
            $data = Projects::where('id', $id)->first();
            $data->client_status = $request->status;
            $data->update();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // - Proposals
    public function proposalAction ($id, Request $request) {
        try {
            // if (!empty($request->action)) {
                $condition = [];

                if ($request->has('action') && $request->action === 'accept') $condition = array_merge($condition, [
                    'isAccepted' => 1,
                ]);

                if ($request->has('action') && $request->action === 'decline') $condition = array_merge($condition, [
                    'isDeclined' => 1
                ]);

                $proposal = Proposals::where('id', $id)->with('project')->first();
            
                if (!empty($condition)) {
                    if ($request->action === 'accept') {
                        $otherProposals = Proposals::where(
                            'project_id', $proposal->project_id
                        )->where(
                            'id', '!=', $proposal->id     
                        )->get();                    
                        
                        foreach ($otherProposals as $other) {
                            $other->isDeclined = 1;
                            $other->isAccepted = 0;
                            $other->update();
                        }
                    }

                    $proposal->update($condition);

                    $project = Projects::where('id', $proposal->project_id)->first();
                    $project->merchant_id = $proposal->merchant_id;
                    $project->update();

                    $merchant = $proposal->merchant;
                    $merchant->notify(new MerchantProposalAccepted($merchant, $project));

                    $client = auth()->user();
                    $loggedInUser = User::where('id', $client->id)->first();
                    $loggedInUser->notify(new ClientAcceptedProposal($loggedInUser, $project));
                }

                // if ($request->has('action') && $request->action === 'accept') {
                //     $project = Projects::where('id', $proposal->project_id)->first();
                //     $project->status = 'active';
                //     $project->update();
                // }

                return response()->json($proposal, 200);
            // } else abort(500, 'Proposal action needed');    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }  
}
