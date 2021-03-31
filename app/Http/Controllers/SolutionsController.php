<?php

namespace App\Http\Controllers;

use App\CyberPalReviews;
use Illuminate\Http\Request;
use App\Http\Resources\SolutionCollection;
use App\Solution;
use App\Feature;
use App\ResellerSolution;
use App\FeatureSolution;
use App\Company;
use DB;

class SolutionsController extends Controller
{
    public function index (Request $request)
    {        
        $data = [];
        $condition = [];
        $paginate = 50;

        if ($request->has('tab') && $request->tab !== 'all') $condition = array_merge($condition, [
            'organisation_size' => $request->query('tab')
        ]);

        if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
            'category_id' => $request->query('category')
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try {
            $data = Solution::where($condition)->orderBy('updated_at', 'DESC')->paginate($paginate);
            
            return new SolutionCollection($data, 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {      
       
        try {
            $object = $request->cyberpal_review;

            $review = new CyberPalReviews([
                'rating' => $object['rating'],
                'review' => $object['review']
            ]);
            $review->save();

            if (empty($review->id)) {
                return response()->json(['error' => 'Something went wrong. Please try again!!!'], 500);
            }            

            $request->request->add(['cyberpal_review_id' => $review->id]);
            
        
            $solutionCompany = $request->company;
            $company_id = 0;
            if($solutionCompany!=null && isset($solutionCompany['id']))
            {

                $request->company = $solutionCompany['company_name'];
                $company_id = $solutionCompany['id'];
                if($company_id==-1)
                {
                    $company = new Company();
                    $company->company_name = $solutionCompany['company_name'];
                    $company->save();
                    $company_id = $company->id;
                
                }
            }
            
            $post = new Solution($request->except('cyberpal_review','standardFeatures','uniqueFeatures'));

            $post->company_name = $company_id;
            $post->save();

            $this->listResellers($request, $post->id);
            $this->set_feature($request, $post->id);

            return response()->json([
                'message' => 'Successfully stored solutions.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function listResellers($request, $sol_id){
        
        $request->headers->set('Access-Control-Allow-Origin', '*');
        
        if(isset($request->reseller_id) && $request->reseller_id != ''){
            
            for( $i=0; $i < count($request->reseller_id); $i++){

                
                $query = current(DB::select(DB::RAW("select * from reseller_solution where reseller_id = ".$request->reseller_id[$i]." and solution_id = ".$sol_id."")));

                if(isset($query) && $query != ''){
                
                }else{
                    $data = [];
                    $data = new ResellerSolution();
                    $data->reseller_id = $request->reseller_id[$i];
                    $data->solution_id = $sol_id;
                    $data->save();
                }

            }
                         
        }
        else{
            $query = DB::select(DB::RAW("SELECT `id`, `name` FROM `resellers`"));
            return $query;
        }
        
    }

    public function update_reseller_solution($request, $sol_id){

        $request->headers->set('Access-Control-Allow-Origin', '*');

        if(isset($request->reseller_id) && $request->reseller_id != ''){


            DB::select(DB::RAW("DELETE FROM `reseller_solution` WHERE solution_id = ".$sol_id.""));
            
            for( $i=0; $i < count($request->reseller_id); $i++){

                
                $query = current(DB::select(DB::RAW("select * from reseller_solution where reseller_id = ".$request->reseller_id[$i]." and solution_id = ".$sol_id."")));

                if(isset($query) && $query != ''){
                }else{
                    $data = [];
                    $data = new ResellerSolution();
                    $data->reseller_id = $request->reseller_id[$i];
                    $data->solution_id = $sol_id;
                    $new_id = $data->save();
             
                }

            }
                         
        }
    }


    public function getAnalytics(Request $request){

        $app = app();
        $data = $app->make('stdClass');

        $categories = array("organization_size", "industry", "solution_type", "budget", "implementation_estimate", "mssp_var", "emerging_vendors", "requirements");


        $newData = [];
        
        $country1 = ''; $city1 = ''; $date1 = ''; $category1 = '';
        if(isset($request->country) && $request->country != ''){
            $country1 = " AND LOWER(country) = '$request->country'";
        }
        if(isset($request->city) && $request->city != ''){
            $city1 = " AND LOWER(city) = '$request->city'";
        }
        if(isset($request->from) && $request->from != '' && $request->to == ''){
            $date1  = " and DATE(created_at) >= '$request->from' and DATE(created_at) <= CURDATE() ";
        }
        if(isset($request->to) && $request->to != '' && $request->from == ''){
            $date1  = " and DATE(created_at) <= '$request->to' ";
        }
        if(isset($request->from) && $request->from != '' && isset($request->to) && $request->to != ''){
            $date1 = " and DATE(created_at) BETWEEN '$request->from' AND '$request->to' ";
        }
        if(isset($request->category) && $request->category){
            $category_q = "SELECT `id`, `name` FROM `categories` WHERE `name` LIKE '%$request->category%'";
            $category_q = current(DB::select($category_q));
            $category1   = " AND category = '$category_q->id' ";
        }
        $total = "select
            sum(case when user_id = -1 $date1 $country1 $city1 $category1 then 1 else 0 end) as anonymous_total,
            sum(case when user_id != -1 $date1 $country1 $city1 $category1 then 1 else 0 end) as registered_total 
            from analytics_master";

        $total = DB::select($total);

        $newData['total'] = $total;

        $country = ''; $city = ''; $date = ''; $category = '';
        for($i=0; $i < count($categories); $i++){
            if(isset($request->country) && $request->country != ''){
                $country = " AND LOWER(country) = '$request->country'";
            }
            if(isset($request->city) && $request->city != ''){
                $city = " AND LOWER(city) = '$request->city'";
            }
            if(isset($request->from) && $request->from != '' && $request->to == ''){
                $date  = " and DATE(created_at) >= '$request->from' and DATE(created_at) >= CURDATE() ";
            }
            if(isset($request->to) && $request->to != '' && $request->from == ''){
                $date  = " and DATE(created_at) <= '$request->to' ";
            }
            if(isset($request->from) && $request->from != '' && isset($request->to) && $request->to != ''){
                $date = " and DATE(created_at) BETWEEN '$request->from' AND '$request->to' ";
            }
            if(isset($request->category) && $request->category){
                $category_q = "SELECT `id`, `name` FROM `categories` WHERE `name` LIKE '%$request->category%'";
                $category_q = current(DB::select($category_q));
                $category   = " AND category = '$category_q->id' ";
            }

            $query_template = "select CONCAT(UCASE(LEFT($categories[$i], 1)), 
            SUBSTRING($categories[$i], 2)) as $categories[$i],
            sum(case when user_id = -1 
            $date
            $country $city $category
            then 1 else 0 end) as anonymous,
            sum(case when user_id != -1 $date 
            $country $city $category 
            then 1 else 0 end) as registered 
            from analytics_master group by $categories[$i]";
            $newData[$categories[$i]] = DB::select($query_template);
        }

        return response()->json($newData, 200);
    }

    

    public function get_country_city(Request $request){
        $country_s = "select DISTINCT country from analytics_master ORDER BY country asc";
        $country_s = DB::select($country_s);
        if(isset($request->country) && $request->country != ""){
            $city_s = "select DISTINCT city from analytics_master WHERE LOWER(country) = '$request->country' ORDER BY city asc";
            $city_s = DB::select($city_s);
            $city = array();
            for($i=0; $i<count($city_s); $i++){
                if($city_s[$i]->city != ''){
                    array_push($city,$city_s[$i]->city);
                }
            }
            return response()->json($city, 200);
        }

        $country = array();
        for($i=0; $i<count($country_s); $i++){
            if($country_s[$i]->country != ''){
                array_push($country,$country_s[$i]->country);
            }
        }

        $categories_s = DB::select("SELECT name from categories categories order by name asc");
        $categories = array();
        for($i=0; $i<count($categories_s); $i++){
            if($categories_s[$i]->name != ''){
                array_push($categories,$categories_s[$i]->name);
            }
        }

       $app = app();
       $data = $app->make('stdClass');
       $data->country = $country;
       $data->categories = $categories;

       return response()->json($data, 200);
    }

    public function show($id)
    {
        try {
            $solutions = Solution::where('id', $id)->with('cyberpalReview')->first();

           

            $query = DB::select(DB::RAW("SELECT `reseller_id` FROM `reseller_solution` WHERE `solution_id` = $id"));
            
            if(isset($query) && $query != '' ){
                $new_data = array();
                for($i = 0; $i < count($query); $i++){
                    array_push($new_data, $query[$i]->reseller_id);
                }
                $solutions->reseller_id = $new_data;
            }


            $query = DB::select("SELECT fs.feature_id as id, fs.solution_id , fm.feature_name, fm.type  FROM feature_solution fs INNER JOIN feature_master fm ON fm.id = fs.feature_id WHERE fs.solution_id = $id");
            $solutions->features_list = $query;
           
            if(isset($query) && $query != ''){
                $standardFeatures = array();
                $uniqueFeatures = array();

                for($i=0; $i < count($query); $i++){
                    if($query[$i]->type == "unique"){
                        array_push($uniqueFeatures,$query[$i] );
                    }else if($query[$i]->type == "standout"){
                        array_push($standardFeatures,$query[$i] );
                    }
                }
            }
            $solutions->standardFeatures = $standardFeatures;
            $solutions->uniqueFeatures   = $uniqueFeatures;

            return response()->json([
                'message' => 'Successfully fetched solution.',
                'data' => $solutions
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        
        $solutionCompany = $data['company'];
        $company_id = 0;
        if($solutionCompany!=null && isset($solutionCompany['id']))
        {
            $company_id = $solutionCompany['id'];
            if($company_id==-1)
            {
                $company = new Company();
                $company->company_name = $solutionCompany['company_name'];
                $company->save();
                $company_id = $company->id;
               
            }
        }
        
        try {
            
            $object = $request->cyberpal_review;
            $review = CyberPalReviews::where('id', $request->cyberpal_review_id)->first();
            $review->update($object);

            $post = Solution::where('id', $id)->first();
            $post->update($request->except('rating', 'cyberpal_review'));
            
            $post->company_name = $company_id;
            $post->save();

            $this->update_reseller_solution($request, $id);
            $this->update_features($request, $id);

            return response()->json([
                'message' => 'Successfully updated solution.',
                'data' => $post
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $data = Solution::where('id', $id)->first();
            $review = CyberPalReviews::where('id', $data->cyberpal_review_id)->first();
            $review->delete();
            $data->delete();

            return response()->json([
                'message' => 'Successfully fetched solution.',
                'data' => $data
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }


    // - Done
    public function deleteMultipleData(Request $request) {

        try {
            if ($request->ids) {
                Solution::find($request->ids)->each(function ($blog, $key) {
                    $blog->delete();
                });

                return response()->json('Successfully deleted categories.', 200);
            } else response()->json('Operation Not permitted', 400); 
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    // - Done
    public function searchData(Request $request) {
        $data = [];
        $condition = [];
        $paginate = 12;
        
        if ($request->has('tab') && $request->tab !== 'all') $condition = array_merge($condition, [
            'organisation_size' => $request->tab
        ]);

        if ($request->has('category') && $request->category !== 'all') $condition = array_merge($condition, [
            'category_id' => $request->category
        ]);

        if ($request->has('paginate')) $paginate = $request->query('paginate');

        try { 
            if (!empty($request->q)) {
                $data = Solution::where('title', 'LIKE', '%'.$request->q.'%')->where($condition)->with('category')->paginate($paginate);
                
                return response()->json($data, 200);
            } else return response()->json('Operation not allowed', 500);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function set_feature($request, $sol_id){
        $request->headers->set('Access-Control-Allow-Origin', '*');
        if(isset($request->standardFeatures) && $request->standardFeatures != ''){
            
            for( $i=0; $i < count($request->standardFeatures); $i++){

                
                $query = current(DB::select(DB::RAW("select * from feature_solution where feature_id = ".$request->standardFeatures[$i]['id']." and solution_id = ".$sol_id."")));
               
                if($request->standardFeatures[$i]['id'] == -1){
           
                    $data = [];
                    $feature = new Feature();
                    $feature->feature_name = $request->standardFeatures[$i]['feature_name'];
                    $feature->type = 2;
                    $feature->category = $request->category_id;
                    $feature->created_at = date('Y-m-d');
                    $feature->save();
                    

                    $data = [];
                    $FeatureSolution = new FeatureSolution();
                    $FeatureSolution->feature_id = $feature->id;
                    $FeatureSolution->solution_id = $sol_id;
                    $FeatureSolution->type  = 2;
                    $FeatureSolution->created_at = date('Y-m-d');
                    $FeatureSolution->save();
                }
                else
                {
                    
                    $data = [];
                    $FeatureSolution = new FeatureSolution();
                    $FeatureSolution->feature_id = $request->standardFeatures[$i]['id'];
                    $FeatureSolution->solution_id = $sol_id;
                    $FeatureSolution->type  = 2;
                    $FeatureSolution->created_at = date('Y-m-d');
                    $FeatureSolution->save();
                }
            }
                         
        }
        else
        {
            $query = DB::select(DB::RAW("SELECT `id`, `feature_name` FROM `feature_master`"));
            return $query;
        }

        // for unique feature
        if(isset($request->uniqueFeatures) && $request->uniqueFeatures != ''){
            print_r($request->category_id);
            
            for( $i=0; $i < count($request->uniqueFeatures); $i++){

                
                $query = current(DB::select(DB::RAW("select * from feature_solution where feature_id = ".$request->uniqueFeatures[$i]['id']." and solution_id = ".$sol_id."")));

                if(isset($query) && $query != ''){
                    
                }else{
                    if($request->uniqueFeatures[$i]['id'] == -1){
              
                        $data = [];
                        $Feature = new Feature();
                        $Feature->feature_name = $request->uniqueFeatures[$i]['feature_name'];
                        $Feature->type = 1;
                        $feature->category = $request->category_id;
                        $Feature->created_at = date('Y-m-d');
                        $Feature->save();

                        $data = [];
                        $FeatureSolution = new FeatureSolution();
                        $FeatureSolution->feature_id = $Feature->id;
                        $FeatureSolution->solution_id = $sol_id;
                        $FeatureSolution->type  = 1;
                        $FeatureSolution->created_at = date('Y-m-d');
                        $new_id = $FeatureSolution->save();
                    }
                    else
                    {
                       
                        $data = [];
                        $FeatureSolution = new FeatureSolution();
                        $FeatureSolution->feature_id = $request->uniqueFeatures[$i]['id'];
                        $FeatureSolution->solution_id = $sol_id;
                        $FeatureSolution->type  = 1;
                        $FeatureSolution->created_at = date('Y-m-d');
                        $FeatureSolution->save();
                    }
                }

            }
                         
        }
        else
        {
            $query = DB::select(DB::RAW("SELECT `id`, `feature_name` FROM `feature_master`"));
            return $query;
        }
    }

    public function update_features($request, $sol_id){
        // $request->headers->set('Access-Control-Allow-Origin', '*');

        DB::select(DB::RAW("DELETE FROM `feature_solution` WHERE solution_id = ".$sol_id.""));
        if(isset($request->standardFeatures) && $request->standardFeatures != ''){
            
            for( $i=0; $i < count($request->standardFeatures); $i++){

                
                $query = current(DB::select(DB::RAW("select * from feature_solution where feature_id = ".$request->standardFeatures[$i]['id']." and solution_id = ".$sol_id."")));

                if(isset($query) && $query != ''){
                }else{
                    if($request->standardFeatures[$i]['id'] == -1){
                        $data = [];
                        $data = new Feature();
                        $data->feature_name = $request->standardFeatures[$i]['feature_name'];
                        $data->type = 2;
                        $data->category = $request->category_id;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                        $new_id = current(DB::select("SELECT * FROM `feature_master` ORDER BY  id DESC"));

                        $data = [];
                        $data = new FeatureSolution();
                        $data->feature_id = $new_id->id;
                        $data->solution_id = $sol_id;
                        $data->type  = 2;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                    }else{
                        $data = [];
                        $data = new FeatureSolution();
                        $data->feature_id = $request->standardFeatures[$i]['id'];
                        $data->solution_id = $sol_id;
                        $data->type  = 2;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                    }
                }

            }
                         
        }

         if(isset($request->uniqueFeatures) && $request->uniqueFeatures != ''){

            for( $i=0; $i < count($request->uniqueFeatures); $i++){

                
                $query = current(DB::select(DB::RAW("select * from feature_solution where feature_id = ".$request->uniqueFeatures[$i]['id']." and solution_id = ".$sol_id."")));

                if(isset($query) && $query != ''){
                }else{
                    if($request->uniqueFeatures[$i]['id'] == -1){
                        $data = [];
                        $data = new Feature();
                        $data->feature_name = $request->uniqueFeatures[$i]['feature_name'];
                        $data->type = 1;
                        $data->category = $request->category_id;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                        $new_id = current(DB::select("SELECT * FROM `feature_master` ORDER BY  id DESC"));

                        $data = [];
                        $data = new FeatureSolution();
                        $data->feature_id = $new_id->id;
                        $data->solution_id = $sol_id;
                        $data->type  = 1;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                    }else{
                        $data = [];
                        $data = new FeatureSolution();
                        $data->feature_id = $request->uniqueFeatures[$i]['id'];
                        $data->solution_id = $sol_id;
                        $data->type  = 1;
                        $data->created_at = date('Y-m-d');
                        $new_id = $data->save();
                    }
                }

            }
                         
        }
    }
    
    public function get_feature(){
        
        if(isset($_GET['category_id']) && $_GET['category_id'] != '' ){
            $category_id = $_GET['category_id'];
            if(isset($_GET['varient'])){
                $varient = $_GET['varient'];
                $query = DB::select(DB::RAW("SELECT `id`, `feature_name` FROM `feature_master` WHERE `feature_master`.`category` = $category_id and `feature_master`.`type` = $varient "));
                return response()->json([
                    'message' => 'Features featched',
                    'data' => $query
                ], 200);
            }else{
                $query = DB::select(DB::RAW("SELECT `id`, `feature_name` FROM `feature_master` WHERE `feature_master`.`category` = $category_id "));
                return response()->json([
                    'message' => 'Features featched',
                    'data' => $query
                ], 200);
            }
            
        }
        $varient = $_GET['varient'];
        $query = DB::select(DB::RAW("SELECT `id`, `feature_name` FROM `feature_master` WHERE `feature_master`.`type` = $varient "));
        return response()->json([
            'message' => 'Features featched',
            'data' => $query
        ], 200);
    }

    public function getAllFeatures(){
        $query = DB::select('SELECT feature_master.*, categories.name FROM `feature_master` INNER JOIN categories ON feature_master.category = categories.id');
        return response()->json([
            'message' => 'Features featched',
            'data' => $query
        ], 200);
    }

    public function get_companies(){
        $query = DB::select(DB::RAW("SELECT `id`, `company_name` FROM `company_master`"));
        return response()->json([
            'message' => 'Companies featched',
            'data' => $query
        ], 200);
    }

    public function getSingleFeature($id){
        $query = "SELECT feature_master.*, categories.name, categories.id as category_id FROM `feature_master` INNER JOIN categories ON feature_master.category = categories.id WHERE feature_master.id = $id";
        $query = DB::select($query);
        return response()->json([
            'message' => 'Features featched',
            'data' => $query
        ], 200);
    }
    
    public function set_company(Request $request){
         $data = $request->all();

        $validator = Validator::make(
            $data,
            [
                'user_name'     => ['required', 'string', 'max:40'],
                'signup_via'    => ['required', 'string', 'max:3'],
                'sm_id'         => ['required', 'string'],
                'usertype'      => ['required', 'integer', 'max:3'],
                'email'         => ['required', 'string']
            ]
        );
    
    }
}
