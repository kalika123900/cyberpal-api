<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Lead as LeadResource;
use App\Leads;
use App\Solution;
use App\Resellers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SingleLeadExport;
use App\Exports\LeadsExport;
use App\Merchants;
use App\Notifications\MerchantLeadAssigned;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!empty($request->status)) {
                $data = Leads::where('status', $request->status)->orderBy('created_at', "DESC")->paginate(30);
                $leads = [];

                foreach ($data as $lead) {
                    $data = [];

                    if (!empty ($lead->requestedResellers)) {
                        if ($lead->fromWhere === "solution-direct" || $lead->fromWhere === "solutions-search") {
                            foreach ($lead->requestedResellers as $id) {
                                $solution = Solution::where('id', $id)->first();
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
            } else return response()->json('Operation not found', 404);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store(Request $request)
    {
        // - I am not doing validation here
        $post = new Leads();
        $post->save($request->all());
        
        return new LeadResource($post);
    }

    public function show($id)
    {
        $lead = Leads::where('id', $id)->first();
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
        // return new LeadResource(Leads::FindOrFail($id));
    }

    public function update(Request $request, $id)
    {
        //I am doing validation here
        $post = Leads::where('id', $id)->first();
        $post->update($request->all());

        if ($post && $post->merchant_id) {
            $user = Merchants::where('id', $post->merchant_id)->first();
            
            if (!empty($user)) {
                $user->notify(new MerchantLeadAssigned($user));
            }
        }
        
        return new LeadResource($post);
    }
    
    public function destroy($id)
    {
        $post = Leads::findOrFail($id);
        $post->delete();
        
        return new LeadResource($post);
    }

    public function export (Request $request) {
        if ($request->has('format')) {
            if ($request->format === "excel") { 
                return Excel::download(new LeadsExport, 'leads-collection.xlsx');
            } 

            if ($request->format === "csv") {
                return Excel::download(new LeadsExport, 'leads-collection.csv');
            }
        } 

        return response()->json('Operation not permitted.', 400);
    }

    public function exportSingle (Request $request, $id) {
        if ($request->has('format') && !empty($id)) {
            if ($request->format === "excel") { 
                return Excel::download(new SingleLeadExport($id), 'lead.xlsx');
            } 

            if ($request->format === "csv") {
                return Excel::download(new SingleLeadExport($id), 'lead.csv');
            }
        } 

        return response()->json('Operation not permitted.', 400);
    }
}
