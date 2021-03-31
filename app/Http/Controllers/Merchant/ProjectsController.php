<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Merchants;
use App\Projects;
use App\Proposals;
use App\User;
use Illuminate\Http\Request;
use App\Notifications\CustomerProposalAvailable;
use App\Notifications\MerchantProposalSubmitted;

class ProjectsController extends Controller
{
    public function getUserProjectInvites () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $data = User::where('id', $user->id)->with([
                    'merchantInvites' => function ($query) {
                        $query->orderBy('created_at', "DESC");
                        $query->with('user');
                        $query->get();
                    }
                ])->first();

                return response()->json($data, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSingleProjectInvite ($id) {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $project = Projects::where('id', $id)->with('user', 'category')->first();

                $proposal = Proposals::where([
                    'merchant_id' => $user->id,
                    'project_id' => $id
                ])->first();

                $project->proposal = $proposal;

                return response()->json($project, 200);
            } else throw new \Exception('You are not allowed to perform this operation.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500); 
        }
    }

    public function sendInviteProposal (Request $request, $id) {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $proposal = new Proposals();
                $proposal->cover = $request->cover;
                $proposal->proposed_price = $request->proposed_price;
                $proposal->proposed_timeline = $request->proposed_timeline;
                $proposal->merchant_id = $user->id;
                $proposal->project_id = $id;

                if ($request->hasFile('file')) {
                    if ($request->file('file')->isValid()) {
                        $file = $request->file('file');
                        $name = "proposal_".uniqid().'.'.$file->getClientOriginalExtension();

                        $image['filePath'] = $name;
                        $file->storeAs('public/proposals',$name);
                        $proposal->attachment = 'storage/proposals/'. $name;
                    } else {
                        return response()->json([
                            'error' => 'Invalid file'
                        ], 401);
                    }
                }

                $proposal->save();
                
                $project = Projects::where('id', $proposal->project_id)->first();
                $client = $project->user;

                $client->notify(new CustomerProposalAvailable($client, $project->reference_id));

                $loggedInUser = User::where('id', $user->id)->first();
                $loggedInUser->notify(new MerchantProposalSubmitted($loggedInUser, $project->reference_id));

                return response()->json([
                    'data' => $proposal,
                    'message' => 'Proposal Send.'
                ], 200);
            } else throw new \Exception('You are not allowed to perform this operation.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus ($id, Request $request) {
        try {
            $user = auth()->user();
            // - TODO: Check if loggedin user's is the one awareded with project.
            if (!empty($user)) {
                $data = Projects::where('id', $id)->first();
                $data->merchant_status = $request->status;
                $data->update();
           
                return response()->json($data, 200);
            } else throw new \Exception('You are not allowed to perform this operation.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
