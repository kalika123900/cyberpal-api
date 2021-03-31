<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Leads;
use App\Resellers;
use App\Solution;

class LeadsController extends Controller
{
    public function getAllLeads () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $data = Leads::where('user_id', $user->id)->orWhere('email', $user->email)->orderBy('created_at', 'DESC')->get();
                $leads = [];

                foreach ($data as $lead) {
                    $data = [];

                    if (!empty ($lead->requestedResellers)) {
                        if ($lead->fromWhere === "solution-direct" || $lead->fromWhere === "solutions-search") {
                            foreach ($lead->requestedResellers as $id) {
                                $solution = Solution::where('id', $id)->with('category')->first();
                                array_push($data, $solution);
                            }
                        } else if ($lead->fromWhere === "resellers") {
                            foreach ($lead->requestedResellers as $id) {
                                $reseller = Resellers::where('id', $id)->first();
                                array_push($data, $reseller);
                            }
                        }
                    }
                    
                    $lead['requestedServices'] = $data;
                    array_push($leads, $lead);
                }
                
                return response()->json($leads, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSingleLead ($id) {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $lead = Leads::where('id', $id)->first();
                
                // if ($data->user_id !== $user->id) {
                //     throw new \Exception(("You are not allowed to perform this operation."));
                // }

                // if ($data->email !== $user->email) {
                //     throw new \Exception(("You are not allowed to perform this operation."));
                // }
                $services = [];

                if (!empty ($lead->requestedResellers)) {
                    if ($lead->fromWhere === "solution-direct" || $lead->fromWhere === "solutions-search") {
                        foreach ($lead->requestedResellers as $id) {
                            $solution = Solution::where('id', $id)->first();
                            array_push($services, $solution);
                        }
                    } else if ($lead->fromWhere === "resellers") {
                        foreach ($lead->requestedResellers as $id) {
                            $reseller = Resellers::where('id', $id)->first();
                            array_push($services, $reseller);
                        }
                    }
                }

                $lead['requestedServices'] = $services;

                return response()->json($lead, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
