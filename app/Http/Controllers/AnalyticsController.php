<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leads;
use App\User;
use App\Projects;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\LeadStatus;

class AnalyticsController extends Controller
{

    // - Get Today Data - Leads
    public function getTodayleadsData () { 
        try {
            $user = auth()->user();

            if (!empty($user)) {
                return response()->json([
                    'new' => Leads::where([
                        'status' => 'new',
                        'merchant_id' => $user->id
                    ])->whereDate('created_at', Carbon::today())->count(),
                    'active' => Leads::where([
                        'status' => 'active',
                        'merchant_id' => $user->id
                    ])->whereDate('created_at', Carbon::today())->count(),
                    'total' => Leads::where([
                        'merchant_id' => $user->id
                    ])->whereDate('created_at', Carbon::today())->count(),
                    'reviewing' => Leads::where([
                        'merchant_id' => $user->id,
                        'status' => 'reviewing',
                    ])->whereDate('created_at', Carbon::today())->count(),
                    'failed' => Leads::where([
                        'merchant_id' => $user->id,
                        'status' => 'failed',
                    ])->whereDate('created_at', Carbon::today())->count(),
                    'completed' => Leads::where([
                        'status' => 'completed',
                        'merchant_id' => $user->id
                    ])->whereDate('created_at', Carbon::today())->count(),
                ], 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // - Get Today Data - Projects
    public function getTodayProjectsData () {
        try {
            $user = auth()->user();
        
            if (!empty($user)) {
                $response = [
                    'processing' => 0,
                    'completed' => 0,
                    'active' => 0,
                    'failed' => 0,
                    'reviewing' => 0,
                    'total' => 0
                ];

                $user = User::where('id', $user->id)->first();
                $data = $user->merchantInvites()->whereDate('created_at', Carbon::today())->get();

                foreach ($data as $project) {
                    if ($project->status === "processing") {
                        $response['processing'] = $response['processing'] + 1;
                    }
                    if ($project->status === "completed") {
                        $response['completed'] = $response['completed'] + 1;
                    }
                    if ($project->status === "active") {
                        $response['active'] = $response['active'] + 1;
                    }
                    if ($project->status === "failed") {
                        $response['failed'] = $response['failed'] + 1;
                    }
                    if ($project->status === "reviewing") {
                        $response['reviewing'] = $response['reviewing'] + 1;
                    }
                }

                $response['total'] = count($data);
                
                return response()->json($response, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // - Get Overall Data - Projects
    public function getOverallProjectsData () {
        try {
            $user = auth()->user();
        
            if (!empty($user)) {
                $response = [
                    'processing' => 0,
                    'completed' => 0,
                    'active' => 0,
                    'failed' => 0,
                    'reviewing' => 0,
                    'total' => 0
                ];

                $data = User::where('id', $user->id)->first();
                
                foreach ($data->merchantInvites as $project) {
                    if ($project->status === "processing") {
                        $response['processing'] = $response['processing'] + 1;
                    }
                    if ($project->status === "completed") {
                        $response['completed'] = $response['completed'] + 1;
                    }
                    if ($project->status === "active") {
                        $response['active'] = $response['active'] + 1;
                    }
                    if ($project->status === "failed") {
                        $response['failed'] = $response['failed'] + 1;
                    }
                    if ($project->status === "reviewing") {
                        $response['reviewing'] = $response['reviewing'] + 1;
                    }
                }

                $response['total'] = count($data->merchantInvites);
                
                return response()->json($response, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // - Get Overall Data - LEads
     public function getOverallLeadsData () { 
        try {
            $user = auth()->user();

            if (!empty($user)) {
                  return response()->json([
                   'new' => Leads::where([
                        'status' => 'new',
                        'merchant_id' => $user->id
                    ])->count(),
                    'in-progress' => Leads::where(
                        'merchant_id', $user->id
                    )->whereIn(
                        'status',['proposal','qualifying'])->count(),
                    'total' => Leads::where([
                        'merchant_id' => $user->id
                    ])->count(),
                    'closed' => Leads::where(
                        'merchant_id', $user->id
                    )->whereIn(
                        'status', ['lost','won'])->count(),
                   
                ], 200);
            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getNewLeadsData(){
        try {
            $user = auth()->user();

            if (!empty($user)) {
                  return response()->json(Leads::where([
                        'status' => 'new',
                        'merchant_id' => $user->id
                   ])->with('solutions')->get()
                , 200);
            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getInProgressData(){
        try {
            $user = auth()->user();

            if (!empty($user)) {
                  return response()->json(Leads::where([
                        'merchant_id' => $user->id
                   ])->whereIn('status',['qualifying','proposal'])->with('solutions')->get()
                , 200);
            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getClosedLeadsData(){
        try {
            $user = auth()->user();

            if (!empty($user)) {
                   return response()->json(Leads::where([
                        'merchant_id' => $user->id
                   ])->whereIn('status',['won','lost'])->with('solutions')->get()
                , 200);
            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getLeadDetails(Request $request,$id){
        $leads = Leads::where('id',$id)->with('lead_status')->get()->first();
        return response()->json($leads, 200);
    }
    public function updateLeadStatus(Request $request){
        try {
            $user = auth()->user();

            if (!empty($user)) {

              $leadStatus =  [];   
              $leadStatus['lead_id'] = $request->lead_id;
              $leadStatus['message'] = $request->message;
              $leadStatus['user_id'] = $user->id;  
              $leadStatus['status'] = $request->status;
              $leadStatusData = new LeadStatus($leadStatus);
              $leadStatusData->save();

              $lead = Leads::where('id',$request->lead_id)->get()->first();
              $lead->status = $request->status;
              $lead->save();
            
              $response = [];
              $response['status'] = 'success';
              $response['data'] = $leadStatusData;
              return response()->json($response, 200);

            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getLeadsGraphData (Request $request) { 
        /*
        http://localhost/cyberpal-api/public/api/analytics/leadstatuscount?from=2020-06-30&to=2020-07-30
        */
        // $from = "2020-06-1";
        // $to = "2020-07-30";

        $from =  $request->from;
        $to =  $request->to;

        try {
            $user = auth()->user();

            if (!empty($user)) {
                $dates = DB::table('leads')
                    ->select(DB::raw('DISTINCT date(created_at) as created_at'))
                    ->where('merchant_id', '=', $user->id)
                    ->whereBetween('created_at', array($from, $to))
                    ->get();

                    // $lables = array(); 
                // $data = array();
                // $datasets = array();
                // $dataset = array();
                $response = array();

                foreach ($dates as $date) {
                    $data = [
                        'date' => $date->created_at,
                        'new' => Leads::where([
                            'status' => 'new',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'active' => Leads::where([
                            'status' => 'active',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'total' => Leads::where([
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'dispute' => Leads::where([
                            'merchant_id' => $user->id,
                            'status' => 'dispute',
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'failed' => Leads::where([
                            'merchant_id' => $user->id,
                            'status' => 'failed',
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'completed' => Leads::where([
                            'status' => 'completed',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                    ];

                    array_push($response, $data);
                    // $lables[] = $projdate->created_date;

                    // $projectscount = DB::table('leads')
                    // ->select(DB::raw('count(*) as lead_count, status'))
                    // ->where('merchant_id', '=', 47)
                    // ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $projdate->created_date)
                    // ->groupBy('status')
                    // ->get();
                    // // $dataset['dataset'] = $projectscount; 
                    // $datasets[] = $projectscount; 
                }

                // $data['lables'] = $lables;
                // $data['leadsscount'] = $datasets;
                //return json_encode($data); 
                return response()->json($response, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getExpertsGraphData (Request $request) { 
        /*
        http://localhost/cyberpal-api/public/api/analytics/leadstatuscount?from=2020-06-30&to=2020-07-30
        */
        // $from = "2020-06-1";
        // $to = "2020-07-30";

        $from =  $request->from;
        $to =  $request->to;

        try {
            $user = auth()->user();

            if (!empty($user)) {
                $dates = DB::table('leads')
                    ->select(DB::raw('DISTINCT date(created_at) as created_at'))
                    ->where('merchant_id', '=', $user->id)
                    ->whereBetween('created_at', array($from, $to))
                    ->get();

                    // $lables = array(); 
                // $data = array();
                // $datasets = array();
                // $dataset = array();
                $response = array();

                foreach ($dates as $date) {
                    $data = [
                        'date' => $date->created_at,
                        'new' => Leads::where([
                            'status' => 'new',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'active' => Leads::where([
                            'status' => 'active',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'total' => Leads::where([
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'reviewing' => Leads::where([
                            'merchant_id' => $user->id,
                            'status' => 'reviewing',
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'failed' => Leads::where([
                            'merchant_id' => $user->id,
                            'status' => 'failed',
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                        'completed' => Leads::where([
                            'status' => 'completed',
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count(),
                    ];

                    array_push($response, $data);
                    // $lables[] = $projdate->created_date;

                    // $projectscount = DB::table('leads')
                    // ->select(DB::raw('count(*) as lead_count, status'))
                    // ->where('merchant_id', '=', 47)
                    // ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"), $projdate->created_date)
                    // ->groupBy('status')
                    // ->get();
                    // // $dataset['dataset'] = $projectscount; 
                    // $datasets[] = $projectscount; 
                }

                // $data['lables'] = $lables;
                // $data['leadsscount'] = $datasets;
                //return json_encode($data); 
                return response()->json($response, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getprojectcountData(Request $request) { 
        $id = 9;
        /*
        http://localhost/cyberpal-api/public/api/analytics/projectcount?from=2020-06-30&to=2020-07-30
        */

        $from =  $request->from;
        $to =  $request->to;

       $projectsdate = DB::table('projects')
            ->select(DB::raw('DISTINCT date(created_at) as created_date'))
            ->where('merchant_id', '=', $id)
            ->whereBetween('created_at', array($from, $to))
            ->get();

        $lables = array(); 
        $data = array();
        $datasets = array();
        $dataset = array();

        foreach ($projectsdate as $projdate) {
            $lables[] = $projdate->created_date;
            $projectscount = DB::table('projects')
            ->select(DB::raw('count(*) as project_count, status'))
            ->where('merchant_id', '=', $id)
            ->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),$projdate->created_date)
            ->groupBy('status')
            ->get();
            $dataset['dataset'] =$projectscount; 
            $datasets[] = $dataset; 
        }
        $data['lables'] = $lables;
        $data['projectscount'] = $datasets;

        //return json_encode($data); 
        return response()->json($data, 200);

    }

    public function getWebsiteClicksData (Request $request) {
        $from =  $request->from;
        $to =  $request->to;

        try {
            $user = auth()->user();

            if (!empty($user)) {
                $dates = DB::table('leads')
                    ->select(DB::raw('DISTINCT date(created_at) as created_at'))
                    ->where('merchant_id', '=', $user->id)
                    ->whereBetween('created_at', array($from, $to))
                    ->get();

                $response = array();

                foreach ($dates as $date) {
                    $data = [
                        'date' => $date->created_at,
                        'total' => Leads::where([
                            'merchant_id' => $user->id
                        ])->whereDate('created_at', '=', $date->created_at)->count()
                    ];

                    array_push($response, $data);
                }

                return response()->json($response, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }    
    }

    public function overallClicksData () {
         try {
            $user = auth()->user();

            if (!empty($user)) {
                  return response()->json([
                    'today' => Leads::where([
                        'merchant_id' => $user->id,
                        'created_at' => Carbon::now()
                    ])->count(),
                    'week' => Leads::where([
                        'merchant_id' => $user->id
                    ])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
                    'month' => Leads::where([
                        'merchant_id' => $user->id
                    ])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count(),
                    'overall' => Leads::where([
                        'merchant_id' => $user->id
                    ])->count()
                ], 200);
            } else throw new \Exception("You are not allowed to perform this operation.");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

/*
    public function getleadstatuscountData() { 
         $id = 8;
         $leadscount = DB::table('leads')
                     ->select(DB::raw('count(*) as lead_count, status'))
                     ->where('merchant_id', '=', $id)
                     ->groupBy('status')
                     ->get();
        $resp['leadcount'] =$leadscount;  
        $resp['status'] = 'success'; 
        $resp['status_code'] = 200; 
        $resp['message'] = 'Sucessfully Done!';   
        header('Content-type: application/json');
        echo json_encode($resp);
    }
*/
}
