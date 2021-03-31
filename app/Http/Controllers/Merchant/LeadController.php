<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Leads;
use App\User;
use App\Solution;

class LeadController extends Controller
{
    // # - Finalised
    public function all (Request $request) {
        try {
            $user = auth()->user();

            if (!empty($user) && $user->user_type === "merchant") {
                $condition = [
                    'merchant_id' => $user->id
                ];

                if ($request->has('status') && $request->status !== 'all') $condition = array_merge($condition, [
                    'status' => $request->query('status')
                ]);
                
                $data = Leads::where($condition)->orderBy('created_at', 'DESC')->get();

                return response()->json($data, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get ($id) {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $lead = Leads::where('id', $id)->first();

                $loggedInUser = auth()->user()->id;
                $leadAssignedUser = User::where('id', $lead->merchant_id)->pluck('id')->first();

                if ($loggedInUser === $leadAssignedUser) {
                    return response()->json($lead, 200);
                } else throw new \Exception(("You are not allowed to perform this operation."));
            } else throw new \Exception('You are not allowed to perform this operation.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500); 
        }
    }

    public function update (Request $request, $id) {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $lead = Leads::where('id', $id)->first();

                $leadAssignedUser = User::where('id', $lead->merchant_id)->pluck('id')->first();

                if (auth()->user()->id === $leadAssignedUser) {
                    $lead->merchant_lead_status = $request->status;
                    $lead->update();

                    return response()->json([
                        'data' => $lead,
                        'message' => 'Lead Status Updated.'
                    ], 200);
                } else throw new \Exception(("You are not allowed to perform this operation."));
            } else throw new \Exception('You are not allowed to perform this operation.');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSingleSolution ($id) {
        try {
            $data = Solution::where([
                'id' => $id,
            ])->first();

            return response()->json([
                'data' => $data,
                'message' => 'Solution Info.'
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'error' => $err->getMessage()
            ], 500); 
        }
    }

    // public function sendInviteProposal (Request $request, $id) {
    //     try {
    //         $user = auth()->user();

    //         if (!empty($user)) {
    //             $request->merge([
    //                 'project_id' => $id,
    //                 'merchant_id' => $user->id
    //             ]);

    //             $data = Proposals::create($request->all());
                
    //             return response()->json([
    //                 'data' => $data,
    //                 'message' => 'Proposal Send.'
    //             ], 200);
    //         } else throw new \Exception('You are not allowed to perform this operation.');
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
