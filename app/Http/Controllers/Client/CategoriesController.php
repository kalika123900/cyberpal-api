<?php

namespace App\Http\Controllers\Client;

use App\Categories;
use App\Solution;
use App\Feature;
use App\SolutionLike;
use App\CategoryGroup;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesCollection;
use Illuminate\Http\Request;
use App\Visitors;

use DB;

class CategoriesController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];
        $condition = [];
        $paginate = 100;

        if ($request->has('type'))  {
            if ($request->type === 'solutions') {
                $condition = array_merge($condition, [
                    'is_in_solutions' => 1
                ]);
            }

            if ($request->type === 'experts') {
                $condition = array_merge($condition, [
                    'is_in_experts' => 1
                ]);
            }

            if ($request->type === 'courses') {
                $condition = array_merge($condition, [
                    'is_in_certifications' => 1
                ]);
            }

            if ($request->type === 'events') {
                $condition = array_merge($condition, [
                    'is_in_events' => 1
                ]);
            }

            if ($request->type === 'community') {
                $condition = array_merge($condition, [
                    'is_in_community' => 1
                ]);
            }
        }

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        if(!empty($condition)) {
            $data = Categories::where($condition)->orderBy('created_at', 'ASC')->paginate($paginate);
        } else {
            $data = Categories::orderBy('created_at', 'ASC')->paginate($paginate);
        }
        
        return new CategoriesCollection($data);
    }

    public function getSingleData ($url, Request $request) {
        $condition = [];

        $user = auth()->user();
        
        if($user){
            $user_id = $user->id;
        }

        DB::enableQueryLog();

        try {

            $data = Categories::where('url', $url)->first();
            
            $query = "select DISTINCT sol.* from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id
            LEFT JOIN feature_solution fs ON sol.id = fs.solution_id ";

            if($request->has('typical_customer') && $request->typical_customer != '' && $request->typical_customer != 'all'){
                $request->typical_customer = explode(',', $request->typical_customer);
                $str = '';
                foreach($request->typical_customer as $org){
                    $str .= "'".$org."',";
                }
                $trim = trim($str, ' ,');
                $query .= "INNER join json_table (
                            sol.typical_customers,
                            '$[*]' COLUMNS( 
                                lang VARCHAR(255) PATH '$'
                            ) 
                        ) as js on js.lang  IN ($trim)";
            }

            $query .= " WHERE cat.url = '$url' ";

            
            if($request->has('market_sagment') && $request->market_sagment != '' && $request->market_sagment != 'all'){
                $request->market_sagment = explode(',', $request->market_sagment);
                $str = '';
                foreach($request->market_sagment as $org){
                    $str .= "'".$org."',";
                }
                $trim = trim($str, ' ,');
                $query .= ' and organisation_size IN ('.$trim.')';
            }

            if ($request->has('deployment') && $request->deployment!=''){
                $request->deployment = explode(',', $request->deployment);
                foreach($request->deployment as $dep){
                    $query .= ' and '.$dep.' = 1';
                }
            }

            if($request->has('features') && $request->features!=''){
                $str = '';
                $str = $request->features;
                $query .= ' and fs.feature_id IN ('.$str.')';
            }

            if($request->has('sort') && $request->sort == 'recent'){
                $query .= " ORDER BY sol.created_at DESC";
            }
            else if($request->has('sort') && $request->sort == 'score'){
                $query .= " ORDER BY sol.score DESC";
            }
            else if($request->has('sort') && $request->sort == 'rating'){
                $query .= " ORDER BY sol.star_rating DESC";
            }
            else if($request->has('sort') && $request->sort == 'cumulativeonlinerating'){
                $query .= " ORDER BY sol.commutative_rating DESC";
            }
            else if($request->has('sort') && $request->sort == 'alphabetical'){
                $query .= " ORDER BY sol.title ASC ";
            }
            
            //start_rating
            
            // return $query;

            $solutionDetails = DB::select(DB::raw($query));

            $cyberpalReview = [];
            $cyberpalSolutionMarking = [];
            $cyberpalVote = [];
            $userVote = [];

            if(count($solutionDetails)){
                foreach($solutionDetails as $solDO){
                    $solD = (array)$solDO;
                    $solD = $solD['id'];
                    $cyberpalReview = DB::select(DB::raw("select cw.* from cyberpal_reviews cw INNER JOIN solutions sol ON sol.cyberpal_review_id = cw.id WHERE sol.id = $solD "));
                    $cyberpalSolutionMarking = DB::select(DB::raw("SELECT csm.* FROM solutions sol INNER JOIN cyberpal_solution_marking csm ON csm.solution_id = sol.id WHERE sol.id =  $solD "));
                    $cyberpalVote = DB::select(DB::raw("SELECT sl.* FROM solution_like sl INNER JOIN solutions sol ON sl.solution_id = sol.id WHERE sol.id = $solD"));
                    $solDO->userVote = [];
                    if(isset($user_id) && $user_id != ''){
                        $uV = DB::select(DB::raw("SELECT sl.* FROM solution_like sl INNER JOIN solutions sol ON sl.solution_id = sol.id WHERE sol.id = $solD and sl.user_id = $user_id"));
                        $solDO->userVote = current($uV);
                    }
                    $solDO->solution_marking = current($cyberpalSolutionMarking);
                    $solDO->votes = $cyberpalVote;
                    $solDO->cyberpalReview = $cyberpalReview;
                }
            }

            /*Code for counting views*/   
            $visitors = new Visitors();
            $visitors->page_type = 'category';
            $visitors->page_id = $data->id;
            $visitors->ip_address = $_SERVER['REMOTE_ADDR'];
            $visitors->page_title = $data->name;
            $visitors->save();
            $cat_id = $data->id;
            $featuresList = DB::select("select id, feature_name from feature_master where category = $cat_id ORDER BY feature_name DESC");

            $data->solutions = $solutionDetails;
            $data->featureList = $featuresList;

            $innovators = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$url' AND sol.vendor_status = 'Innovators'"));
            
            
            $pioneers = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$url' AND sol.vendor_status = 'Pioneer'"));
            


            $successor = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$url' AND sol.vendor_status = 'Successor'"));
         


            $emerging = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$url' AND sol.vendor_status = 'Emerging'"));
            
            $data->pyramid = array(
                "innovators" => $innovators,
                "pioneers" => $pioneers,
                "successor" => $successor,
                "emerging" => $emerging
            );


            return response()->json($data, 200);
        } catch (\Exception $err) {
            return response()->json([
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function searchData (Request $request) {
        $condition = ['is_in_solutions' => 1];
        $paginate = 15;

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        if ($request->has('type'))  {
            if ($request->type === 'solutions') {
                $condition = array_merge($condition, [
                    'is_in_solutions' => 1
                ]);
            }

            if ($request->type === 'experts') {
                $condition = array_merge($condition, [
                    'is_in_experts' => 1
                ]);
            }

            if ($request->type === 'courses') {
                $condition = array_merge($condition, [
                    'is_in_certifications' => 1
                ]);
            }

            if ($request->type === 'events') {
                $condition = array_merge($condition, [
                    'is_in_events' => 1
                ]);
            }

            if ($request->type === 'community') {
                $condition = array_merge($condition, [
                    'is_in_community' => 1
                ]);
            }

            if ($request->type === "popular-searches") {
                $condition = array_merge($condition, [
                    'is_in_top_searches' => 1
                ]);
            }
        }

        try {
            if (!empty($request->q)) {
                $data = Categories::where('name', 'LIKE', '%'.$request->q.'%')->where($condition)->paginate($paginate);
                
                return new CategoriesCollection($data);
            } else {
                $data = Categories::where($condition)->paginate($paginate);

                return new CategoriesCollection($data);
            }
        } catch (\Exception $err) {
            return response()->json([
                'error' => $err->getMessage()
            ], 500);
        }
    }

    public function getBestFit(Request $request) {

        $user = auth()->user();
        
        if($user){
            $user_id = $user->id;
        }
       
        $url = "category=".urlencode($request->category)."&industry=".urlencode($request->industry)."&organizationSize=".urlencode($request->organizationSize)."&deployment=".urlencode($request->deployment)."&implementationTime=".urlencode($request->implementationTime)."&currentSolution=".urlencode($request->currentSolution)."&generation=".urlencode($request->generation)."&budget=".urlencode($request->budget)."&easeOfUseEnabled=".urlencode($request->easeOfUseEnabled)."&managedServiceProvider=".urlencode($request->managedServiceProvider)."&topEmerging=".urlencode($request->topEmerging)."&integrations=".urlencode($request->integrations)."&features=".urlencode($request->features);
        $category_id = $request->category;
       
        $error = '';

        $ch = curl_init();
        $headers = array(
        'Content-Type: application/json',
        'Connection: keep-alive',
        'Accept: */*'
        );
        $url = "http://139.59.83.219:5000/find-best-fit?".$url;
        //$url = "http://dummy.restapiexample.com/api/v1/employees";
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        
        if($response === false)
        $error = curl_error($ch);
        curl_close($ch);
        $response = json_decode($response);
        $category_details = Categories::find($category_id)->toArray();
        
        $data = [];
        $data['solutions'] = [];
        $data['category_detail'] = $category_details;
        

        if(isset($response->status) && $response->status == 1 && count($response->data) > 0){

            $condition = [];

            if ($request->has('organisation_size') && $request->organisation_size !== 'all') $condition = array_merge($condition, [
                'organisation_size' => $request->organisation_size
            ]);
    
            try {
                $data = [];
                foreach($response->data as $re){
                    $d = Solution::where('id', $re)->with([
                            'cyberpalReview',
                            'solutionMarking',
                            'votes'
                        ])->first();
                    $d->userVote = [];
                    if(isset($user_id) && $user_id != ''){
                        $uV = DB::select(DB::raw("SELECT sl.* FROM solution_like sl INNER JOIN solutions sol ON sl.solution_id = sol.id WHERE sol.id = $d->id and sl.user_id = $user_id"));
                        $d->userVote = current($uV);
                    }
                    array_push($data,$d);
                }
                $data['solutions'] = $data;
                $data['category_detail'] = $category_details;
    
                return response()->json([
                    'status' => $response->status,
                    'data'  => $data,
                    'error' => $response->error
                ], 200);
            } catch (\Exception $err) {
                return response()->json([
                    'status'=> 'error',
                    'error' => $err->getMessage()
                ], 500);
            }

        }else{
            return response()->json([
                'status' => $response->status,
                'data'  => $data,
                'error' => $response->error
            ], 200);
        }
    }

    public function categoryBasedFeatures(Request $request) {
        $data = Categories::where('category', '=', $request->category_id)->get();
        if(count($data)){
                return response()->json([
                    'status' => 200,
                    'data'  => $data,
                    'message' => "Data fetched successfully"
                ], 200);
        }else{
            return response()->json([
                'status' => 400,
                'data'  => [],
                'message' => "No Data found"
            ], 200);
        }
    }

    public function getAllCategoryGroup(Request $request){

      $query = "SELECT cg.id, cg.title, cat.id as category_id, cat.name, cat.url, cat.icon  FROM category_group cg INNER join json_table (
                            cg.categories,
                            '$[*]' COLUMNS( 
                                c_id integer PATH '$'
                            ) 
                        ) as ci on true inner join  categories cat ON cat.id = ci.c_id";
      $categories = DB::select(DB::raw($query));

      if(count($categories)){
        return response()->json([
          'status'=> 1,
          'data'=> $categories,
          'message'=>"Data fetched Successfully"
        ]);
      }else{
        return response()->json([
          'status'=> 0,
          'data'=> $categories,
          'message'=>"No data found"
        ]);
      }
    }
    
}
