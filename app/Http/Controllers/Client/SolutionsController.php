<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Solution as ResourcesSolution;
use App\Http\Resources\SolutionCollection;
use App\Solution;
use App\SolutionLike;
use App\ResellerSolution;
use App\Categories;
use App\Resellers;
use App\AnalyticsReview;
use App\SolutionHitLog;
use App\Reviews;
use App\Visitors;
use App\TopSolutions;
use Illuminate\Http\Request;
use DB;
// use Mail;

use Illuminate\Support\Facades\Mail;

class SolutionsController extends Controller
{
    public function getAllData(Request $request)
    {
        $data = [];
        $condition = ['isApproved' => 1];
        // $paginate = 30;

        // $this->setAnalytics($request);

        if (!$request->category) {
            return response()->json(['error' => 'Caregory doesn\'t exist.'], 500);
        }

        if ($request->has('organisation_size') && $request->organisation_size !== 'all') $condition = array_merge($condition, [
            'organisation_size' => $request->query('organisation_size')
        ]);

        // if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
        //     'category_id' => $request->query('category')
        // ]);

        // if ($request->has('paginate')) $paginate = $request->query('paginate');

        $data = Categories::where('id', $request->category)->with([
            'solutions' => function ($query) use ($condition) {
                $query->where($condition);
                $query->orderBy('created_at', 'ASC');
            },
            'solutions.cyberpalReview',
            'solutions.reviews' => function ($query) {
                // - Get Average of ratings also.
                $query->count();
            }
        ])->first();
        
        return new ResourcesSolution($data);
    }

    public function homepage_search(){

        $q = $_GET['string'];
        $vendors = DB::select("select DISTINCT name as users_name, uid from users where user_type = 'merchant' and name LIKE '%$q%' ORDER BY name ASC");
        $categories = DB::select("select DISTINCT name as categories_name, url from categories where  name LIKE '%$q%' ORDER BY name ASC");
        $solutions = DB::select("select DISTINCT title as solutions_name, url from solutions where  title LIKE '%$q%' ORDER BY title ASC");
        // $sol = [];
        // $i = 0;
        // foreach($solutions as $s){
        //     $sol[$i] = $s->solutions_name;
        //     $i++;
        // }
        // $vd = [];
        // $i = 0;
        // foreach($vendors as $s){
        //     $vd[$i] = $s->users;
        //     $i++;
        // }
        // $ca = [];
        // $i = 0;
        // foreach($categories as $s){
        //     $ca[$i] = $s->categories_name;
        //     $i++;
        // }
        return response()->json([
                "vendors" => $vendors,
                "solutions" => $solutions,
                "categories" => $categories
            ], 200);
    }

    public function setAnalytics(){

        $user = auth()->user();

        $request = json_decode(json_encode($_REQUEST));

        $ip = $_SERVER['REMOTE_ADDR'];
        $ipdat = @json_decode(file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip)); 

        $data = new AnalyticsReview();

        if(isset($user->id) && $user->id != ''){
            $data->user_id;
        }

        // echo "<pre>";
        // print_r($request);
        // die();
       
        $data->country                  = $ipdat->geoplugin_countryName;
        $data->city                     = $ipdat->geoplugin_city;
        $data->category                 = $request->category;
        $data->solution_name            = $request->current_solution;
        $data->organization_size        = $request->organisation_size;
        $data->industry                 = $request->industry;
        $data->solution_type            = $request->solution_type;
        $data->budget                   = $request->budget;
        $data->implementation_estimate  = $request->implementation_estimate;
        $data->emerging_vendors         = $request->emerging_vendors;
        $data->requirements             = $request->requirements;
        
        if($data->save()){
            echo "Saved";
        }else{
            echo "something went wrong";
        }
    }
    
    public function track(Request $request){
        $user = auth()->user();

        $ip = $_SERVER['REMOTE_ADDR'];
        $ipdat = @json_decode(file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip)); 

        // $data = new AnalyticsReview();

        if(isset($request->id) && $request->id != '' && $request->id != -1){
            $check = current(DB::select("SELECT * FROM `analytics_master` WHERE `id` = '$request->id'"));
            if($check){
                $date = "$request->key"."_visit_time";
                $updated = DB::select("UPDATE `analytics_master` SET $request->key = '$request->value', $date = CURRENT_TIMESTAMP Where id = '$request->id' ");
                return response()->json([
                    'data' => "success"
                ], 200);
            }
        }else{
            $user_id = -1;
            if(isset($user->id) && $user->id != ''){
                $user_id = $user->id;
            }
            $country = $ipdat->geoplugin_countryName;
            $city =    $ipdat->geoplugin_city;
            $insert = DB::insert(DB::RAW("INSERT INTO `analytics_master` (`country`,`city`,`$request->key`) VALUES ('$country', '$city' , '$request->value') "));
            $id = DB::getPdo()->lastInsertId();
            return response()->json([
                'data' => $id
            ], 200);
        }
    }

    public function solution_hit(Request $request){

        $data = new SolutionHitLog();
        $data->analytics_id = $request->id;
        $data->solution_id = $request->solution_id;
        $date = date('y-m-d h:i:s');
        $data->visit_time = $date;
        if($data->save()){
            return response()->json([
                "sucess"
            ], 200);
        }else{
            return response()->json([
                "something went wrong"
            ], 500);
        }
    }

    public function getSingleData ($url) {

        $releatedCategories = array(
            "Network Security" => array("Next-Generation Firewall", "NAC", "Zero Trust Network Access", "IOT", "VPN"),
            "Endpoint Security" => array("Endpoint Detection And Response", "AV", "Web gateway", "Email gateway", "Encryption"),
            "Security Opertations and Incident Reponse" => array("Security Information And Event Management", "Security Orchestration Automation and Response",
                                                                 "Managed Detection and Response", "Breach And Attack Simulation", "SIRP",
                                                                 "TIP","Log Management", "Log Monitoring solutions", "vul assessment software",
                                                                 "patch management", "Dark web monitoring solutions"),
            "MSSPS & Consultancies" => array("GRC Consultants", "Penetration Testing", "Cyber Security Consultant", "MSSPs", "CSPR"),
            "Mobile Security" => array("MDM", "BYOD"),
            "Data Security" => array("Data Loss Prevention", "Backup Software", "Email encryption", "File analysis and sanitisation"),
            "Cloud Secruity" => array("Cloud Access Security Brokers"),
            "Risk & Compalince"=> array("Training and Awareness", "Governance Risk and Compliance", "Phishing software"),
            "Identity & Access Management" => array("Identity and Access Management", "Privileged Access Management", "Multi-Factor Authentication")
        );

        $user = auth()->user();
        
        if($user){
            $user_id = $user->id;
        }

        $data = Solution::where([
            'url' => $url,
            'isApproved' => 1
        ])->with([ 
            'reviews' => function ($query) {
                $query->where('isApproved', 1);
                $query->limit(3);
            },
            'reviews.user',
            'cyberpalReview',
            'solutionMarking',
            'votes',
            'resellers' => function ($query) {
                $query->where('isPublished', 1);
                $query->limit(5);
            },
            'category'
        ])->first();

        if (empty($data->id)) {
            return response()->json(['error' => 'Solution doesn\'t exist.'], 500);
        }

        
        // $solutionsList = DB::select("select sol.id, sol.title, sol.image, sol.url from solutions sol INNER JOIN categories cat ON sol.category_id = cat.id WHERE cat.id = $data->category_id LIMIT 10 ");
        $categoryName = Categories::select('name')->where('id', $data->category_id)->get()->toArray();
        $categoryKey = '';
        $categoryDet = [];
        $categoryNameCheck = '';
        // if(count($categoryName)){
        //     foreach($releatedCategories as $key => $value){
        //         foreach($value as $val){
        //             if($val == $categoryName[0]['name']){
        //                 $categoryDet = $value;
        //                 $categoryNameCheck = $val;
        //                 }
        //             }
        //         }
        // }

        $relatedTechnologies = [];
        $i = 0;
     

        foreach($releatedCategories as $key => $value){
            
            foreach($value as $val){
                $c = Categories::select('id','name', 'url', 'icon')->where('name', 'LIKE', $val)->get()->toArray();
                if(count($c) && $categoryNameCheck != $c[0]['name']){
                    $relatedTechnologies[$i] = $c[0];
                    $i++;
                }
            }
        }
        
        $catIndex = 0;
        $i = 0;

        foreach($relatedTechnologies as $rt){
            if($categoryName[0]['name'] == $rt['name']){
                $catIndex = $i;
            }
            $i++;
        }

        $newRT = [];
        $i = $catIndex;

       

        for($n=0; $n < count($relatedTechnologies); $n++){
            if(count($newRT) < 6){
                if(count($relatedTechnologies) > $i ){
                    array_push($newRT, $relatedTechnologies[$i]);
                    $i++;
                }else{
                    $i = 0;
                }
            }
        }

        
        $relatedTechnologies = $newRT;

        DB::enableQueryLog();

        $similarSolutions = Solution::where([
            'isApproved' => 1,
            'category_id' => $data->category_id,
        ])->where('id', '!=', $data->id)->with('cyberpalReview')->limit(6)->get();
        

        $randomResellers = Resellers::inRandomOrder()->limit(6)->get();

        $totalReviews = Reviews::where('solution_id', $data->id)->count();
        $overallRating = Reviews::where('solution_id', $data->id)->selectRaw('SUM(rating)/COUNT('.$totalReviews.') AS avg_rating')->first()->avg_rating;
        /*Code for counting views*/   
        $visitors = new Visitors();
        $visitors->page_type = 'solution';
        $visitors->page_id = $data->id;
        $visitors->ip_address = $_SERVER['REMOTE_ADDR'];
        $visitors->page_title = $data->title;
        $visitors->save();

        $category_url = DB::select(DB::raw("select cat.url from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where sol.id = $data->id"));
        $category_url = current($category_url);

        $innovators = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$category_url->url' AND sol.vendor_status = 'Innovators'"));
                
        $pioneers = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$category_url->url' AND sol.vendor_status = 'Pioneer'"));
            
        $successor = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$category_url->url' AND sol.vendor_status = 'Successor'"));
         
        $emerging = DB::select(DB::raw("select sol.vendor_status, sol.url as url, sol.image as image from solutions sol INNER JOIN categories cat ON cat.id = sol.category_id where cat.url = '$category_url->url' AND sol.vendor_status = 'Emerging'"));
            
        $pyramid = array(
            "innovators" => $innovators,
            "pioneers" => $pioneers,
            "successor" => $successor,
            "emerging" => $emerging
        );

        $data->userVote = [];
        if(isset($user_id) && $user_id != ''){
            $uV = DB::select(DB::raw("SELECT sl.* FROM solution_like sl INNER JOIN solutions sol ON sl.solution_id = sol.id WHERE sol.id = $data->id and sl.user_id = $user_id"));
            $data->userVote = current($uV);
        }


        return response()->json([
            'data' => $data,
            'similarSolutions' => $similarSolutions,
            'rating' => [
                'total' => $totalReviews,
                'score' => $overallRating
            ],
            'randomResellers' => $randomResellers,
            'relatedTechnologies' => $relatedTechnologies,
            'pyramid' => $pyramid
        ], 200);
    }

    public function getAllCategorySolutions ($category_url) {
        $category = Categories::where('url', $category_url)->first();

        if (empty($category->id)) {
            return response()->json(['error' => 'Category doesn\'t exist.'], 500);
        }

        $data = Solution::where([
            'category_id' => $category->id,
            'isApproved' => 1
        ])->with('cyberpalReview')->orderBy('created_at', 'ASC')->paginate(12);
        
        return new SolutionCollection($data);
    }

    public function searchData (Request $request) {
        $condition = ['isApproved' => 1];
        $paginate = 12;

        if ($request->has('paginate')) $paginate = $request->query('paginate');
        
        try {
            if (!empty($request->q)) {
                $data = Solution::where(
                    'title', 'LIKE', '%'.$request->q.'%'
                )->where(
                    $condition
                )->paginate(15);
                
                return new ResourcesSolution($data);
            } else {
                $data = Solution::where($condition)->paginate($paginate);
                return new ResourcesSolution($data);
            }
        } catch (\Exception $err) {
            return response()->json([
                'error' => $err->getMessage()
            ], 500);
        }
    } 

    public function listResellers(Request $request){
        
        $request->headers->set('Access-Control-Allow-Origin', '*');
        
        // $query = Resellers::all();
        // echo "<pre>";
        // print_r($query);
        // die();
        if(isset($request->reseller_id) && $request->reseller_id != ''){
            
            for( $i=0; $i < count($request->reseller_id); $i++){

                $query = current(DB::select(DB::RAW("select * from reseller_solution where reseller_id = ".$request->reseller_id[$i]." and solution_id = ".$request->sol_id."")));

                if(isset($query) && $query != ''){
                // echo "<pre>";
                // print_r($query);
                // die();
                }else{
                    $data = [];
                    $data = new ResellerSolution();
                    $data->reseller_id = $request->reseller_id[$i];
                    $data->solution_id = $request->sol_id;
                    $new_id = $data->save();
                    // echo "<pre>";
                    print_r("saved");
                    // die();
                }

            }
                         
        }
        else{
            $query = DB::select(DB::RAW("SELECT `id`, `name` FROM `resellers`"));

            // echo "<pre>";
            // print_r($query);
            // die();
            return $query;
        }
        
        //$query = ResellerSolution::
        
    }
    public function updateLatLngResellers(Request $request){
         $data = Resellers::all();
         foreach($data as $reseller)
         {
                $address = str_replace(' ','+',$reseller->business_address);
                $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key=AIzaSyAkBkCCJ-ml6uMEiEXcPYJlVKEYjW2wTPs';
                //  Initiate curl
                $ch = curl_init();
                // Will return the response, if false it print the response
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Set the url
                curl_setopt($ch, CURLOPT_URL,$url);
                // Execute
                $result=curl_exec($ch);
                // Closing
                curl_close($ch);
                $resultArray = json_decode($result,true);
                $lat = $resultArray['results'][0]['geometry']['location']['lat'];
                $lng = $resultArray['results'][0]['geometry']['location']['lng']; 
                $reseller = Resellers::find($reseller->id);
                $reseller->lat = $lat;
                $reseller->lng = $lng;
                $reseller->save(); 
         }
         $data = Resellers::all();
         return response()->json(['data'=>$data],200);
    }
    public function getResellers (Request $request) {
        $data = [];
        $condition = [
            'isPublished' => 1
        ];
        $paginate = 20;
        if ($request->has('solution') && !empty($request->solution)) $condition = array_merge($condition, [
            'solution_id' => $request->query('solution')
        ]);        
        // if ($request->has('solution') && !empty($request->solution)) $condition = array_merge($condition, [
        //     'solution_id' => $request->query('solution')
        // ]);
        
        // - This location is used as reseller location.
        // - Don't use this. Instead use pivot location data.
        // if ($request->has('location') && !empty($request->location)) $condition = array_merge($condition, [
        //     'location_id' => $request->query('location')
        // ]);

        DB::enableQueryLog();

        if ($request->has('paginate')) $paginate = $request->query('paginate');
        
        if ($request->has('solution') && !empty($request->solution)) {
            $solution = Solution::where('id', $request->solution)->first();
           
            $data = Resellers::whereHas('solutions', function($q) use ($solution){
                $q->where('id', $solution->id);
            })->inRandomOrder('1234')->paginate($paginate);

            $queryLog = DB::getQueryLog();
            return $queryLog;
            return new SolutionCollection($data);
        } else {
            $data = Resellers::where($condition)->inRandomOrder('1234')->paginate($paginate);
            return new SolutionCollection($data);
        }
        // ->wherePivot('location_id', '=', $request->query('location'))

        // $data = Resellers::where($condition)->wherePivot($pivotCondition)->with('location')->orderBy('created_at', 'ASC')->paginate($paginate);
        
    }

    public function getResellerInRange(Request $request){
        $lat = 0; $lng = 0;
        $request = $request->all();
        if(isset($request['lat']) && isset($request['lng'])){
            $lat = $request['lat'];
            $lng = $request['lng'];    
        }
        
        $sol_id = $request['solution_id'];
        $paginate = 20;

        if (isset($request['paginate'])) $paginate = $request['paginate'];
        DB::enableQueryLog();
        
        if($lat != 0 && $lng != 0){

            $data = Resellers::select(DB::raw("resellers.*,( 3959  * acos( cos( radians($lat) ) * cos( radians( resellers.lat ) ) * cos( radians( resellers.lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(resellers.lat)) ) ) AS distance"))->join('reseller_solution as rs','rs.reseller_id','resellers.id')->where(function($query) use($sol_id) {
                $query->where('rs.solution_id',$sol_id);
            })->having('distance','<',100)->orderBy('distance')->paginate($paginate);
            return new SolutionCollection($data);
            // $data = DB::select("SELECT 
            //                     resellers. * , 
            //                     ( 3959  *
            //                     acos( cos( radians($lat) ) * 
            //                     cos( radians( resellers.lat ) ) * 
            //                     cos( radians( resellers.lng ) - radians($lng) ) + sin( radians($lat) ) *
            //                     sin(radians(resellers.lat)) ) ) 
            //                     AS distance 
            //                     FROM resellers 
            //                     INNER JOIN reseller_solution rs ON resellers.id = rs.reseller_id and rs.solution_id = $sol_id
            //                     HAVING distance < 25 
            //                     ORDER BY distance");
            
        }else{
            $data = DB::select("SELECT * FROM resellers INNER JOIN reseller_solution rs ON resellers.id = rs.reseller_id and rs.solution_id = $sol_id");
        }
       
        if(count($data)){
            return response()->json([
                'status' => "success",
                'data' => $data, 
                'message' => "Data successfully fetched"
            ], 200);
        }else{
            return response()->json([
                'status' => "success",
                'data' => [],
                'message' => "No Data Found"
            ], 200);
        }
    }

    public function getSingleReseller ($url) {
        $data = Resellers::where([
            'url' => $url,
            'isPublished' => 1
        ])->with('location', 'solutions')->first();

        if (empty($data->id)) {
            return response()->json(['error' => 'Reseller doesn\'t exist.'], 500);
        }

        return new ResourcesSolution($data);
    }

    public function cyberSecurityInsurances (Request $request) {
        $data = [];
        $condition = ['isApproved' => 1];
        $paginate = 20;

        // if ($request->has('vendors') && $request->vendors !== 'all') $condition = array_merge($condition, [
        //     'vendor' => $request->query('vendors')
        // ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try {
            $category = Categories::where('url', 'cyber-security-insurances')->with([
                'solutions' => function ($query) use ($condition, $paginate) {
                    $query->where($condition);
                    $query->orderBy('created_at', 'ASC');
                    $query->paginate($paginate);
                }
            ])->first();

            if (empty($category->id)) {
                return response()->json(['error' => 'Category doesn\'t exist.'], 500);
            }

            return response()->json([
                'data' => $category,
                'filters' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }

    public function cyberSecurityRecruiters (Request $request) {
        $data = [];
        $condition = ['isApproved' => 1];
        $paginate = 20;

        // if ($request->has('vendors') && $request->vendors !== 'all') $condition = array_merge($condition, [
        //     'vendor' => $request->query('vendors')
        // ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try {
            $category = Categories::where('url', 'cyber-security-recruiters')->with([
                'solutions' => function ($query) use ($condition, $paginate) {
                    $query->where($condition);
                    $query->orderBy('created_at', 'ASC');
                    $query->paginate($paginate);
                }
            ])->first();

            if (empty($category->id)) {
                return response()->json(['error' => 'Category doesn\'t exist.'], 500);
            }

            return response()->json([
                'data' => $category,
                'filters' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTopSolutions () {
        try {
            $data = TopSolutions::with('solution')->limit(4)->get();
            

            return response()->json($data, 200);
        } catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function upvoteSolution(Request $request){

        $request = $request->all();

        $result = SolutionLike::where('user_id','=',$request['user_id'])->where('solution_id','=',$request['solution_id'])->get();

        if(count($result) >= 1){
            $result = current($result->toArray());
            if($result['status'] == $request['status']){
                $sol = SolutionLike::where('id','=',$result['id'])->get()->first();
                $sol->delete();
                return response()->json([
                    'data' => "success",
                    'message' => "vote successfully deleted"
                ], 200);
            }else{
                $sol = SolutionLike::find($result['id']);
                $sol->status = $request['status'];
                $sol->save();
                return response()->json([
                    'data' => "success",
                    'message' => "vote successfully updated"
                ], 200);
            }
        }else{
            $data = [];
            $data = new SolutionLike();
            $data->user_id = $request['user_id'];
            $data->solution_id = $request['solution_id'];
            $data->status =  $request['status'];            
            $data->created_at = date('Y-m-d');
            $data->save();
            return response()->json([
                'data' => "success",
                'message' => "vote successfully inserted"
        ], 200);
        }
    }

    public function sendEmail(Request $request){
        $request = $request->all();
       
        foreach($request['users'] as $user){
            $email_m = $user['email'];
            $user_name = $user['name'];
            $user['subject']   = "Subject";
            $msg   = $request['message'];
            $headers = 'From: support@cyberpal.io';
            $msg .= "\n".$request['url'];
            $user['message'] = $msg;
            $is_sent = Mail::send('mail', ['user' => $user], function ($m) use ($user) {
                $m->from('support@cyberpal.io', 'Cyberpal');
                
                $m->to($user['email'], $user['name'])->subject($user['subject']);
            });
        }

        return response()->json([
            'data' => "success",
            'message' => "Mail Sent Successfully"
        ], 200);
        
    }

    public function solutionsAskMail(Request $request){
        $compnay_id = $request->company_id;
        $data = DB::select(DB::raw("select cm.company_name, cm.email FROM company_master cm INNER JOIN solutions sol ON sol.company = cm.id WHERE cm.id = $compnay_id"));
        if(count($data)){
            foreach($data as $d){
                $d = (array)$d;
                $user['email'] = $d['email'];
                $user['name'] = $d['company_name'];
                $user['subject'] = "Ask Question ";
                $user['message'] = $request->question;
                $user['message'] .= " from : ".$request->person_name. " email : ".$request->email;
                $is_sent = Mail::send('mail', ['user' => $user], function ($m) use ($user) {
                    $m->from('support@cyberpal.io', 'Cyberpal');
                    
                    $m->to($user['email'], $user['name'])->subject($user['subject']);
                });
                return response()->json([
                    'status' => "success",
                    'data' => [],
                    'message' => "Email Sent Successfully"
                ], 200);
            }
        }

        $data = DB::select(DB::raw("SELECT us.name, us.email FROM solutions sol INNER JOIN merchants me ON sol.vendor_id = me.user_id INNER JOIN users us ON me.user_id = us.id WHERE sol.id = $request->solutions_id"));
        if(count($data)){
            foreach($data as $d){
                $d = (array)$d;
                $user['email'] = $d['email'];
                $user['name'] = $d['name'];
                $user['subject'] = "Ask Question ";
                $user['message'] = $request->question;
                $user['message'] .= " from : ".$request->person_name. " email : ".$request->email;
                $is_sent = Mail::send('mail', ['user' => $user], function ($m) use ($user) {
                    $m->from('support@cyberpal.io', 'Cyberpal');
                    
                    $m->to($user['email'], $user['name'])->subject($user['subject']);
                });
                return response()->json([
                    'status' => "success",
                    'data' => [],
                    'message' => "Email Sent Successfully"
                ], 200);
            }
        }
        else{
            return response()->json([
                'status' => "failed",
                'data' => [],
                'message' => "Mail not sent"
            ], 200);
        }
    }

}
