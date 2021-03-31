<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Merchants;
use App\User;
use App\Solution;
use App\Leads;
use App\Visitors;

class ThirdpartyController extends Controller
{
    public function index(Request $request)
    {
    DB::connection()->enableQueryLog();
   
    $yesterday = date('Y-m-d',strtotime('-1 day'));
    $today = date('Y-m-d');
    
    //Query for Previous Day Users
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDayCustomer = count($users);

     //Query for ToDay Users
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $todayCustomer = count($users);

     //Query for Till Day Users
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $customerYTD = count($users);

     //Query for Previous Day Merchants
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDayMerchants = count($users);
     
     //Query for Current Day Merchants
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
     $merchantToday = count($users);

     //Query for Till Day Merchants
     $users =   User::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
     $merchantYTD = count($users);

     //Query for Previous Day Unverified Users
     $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDayPendingEmailCustomer = count($users);
     
     //Query for Current Day Unverified Users
      $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $customerTodayPendingEmail = count($users);

     //Query for Till Day Unverified Users
      $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where("user_type", '=', "customer")
                        ->select(DB::raw('id'))
                        ->get();
     $customerYTDPendingEmail = count($users);

     //Query for Previous Day Unverified Merchants
     $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDayPendingEmailMerchants = count($users);

     //Query for Current Day Unverified Merchants
     $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
     $merchantTodayPendingEmail = count($users);

     //Query for Till Day Unverified Merchants
     $users =   User::where('isVerified','=',"0")
                        ->where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where("user_type", '=', "merchant")
                        ->select(DB::raw('id'))
                        ->get();
    //  $queries = DB::getQueryLog();
    //  $last_query = end($queries);
    //  print_r($last_query); die();                    
     $merchantYTDPendingEmail = count($users);

     //Query for Previous Day Solutions
     $solutions =   Solution::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDaySolutions = count($solutions);

     //Query for ToDay Solutions
     $solutions =   Solution::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->select(DB::raw('id'))
                        ->get();
     $solutionsToday = count($solutions);

      //Query for ToDay Solutions
     $solutions =   Solution::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->select(DB::raw('id'))
                        ->get();
     $solutionsYTD = count($solutions);

     //Query for Previous Day Solutions
     $leads =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->select(DB::raw('id'))
                        ->get();
     $previousDayLeads = count($leads);

     //Query for ToDay Solutions
     $leads =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->select(DB::raw('id'))
                        ->get();
     $leadsToday = count($leads);

     //Query for YTD Leads
     $leads =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->select(DB::raw('id'))
                        ->get();
     $leadsYTD = count($leads);

     //Query for YTD Leads
     $leadsIndustry    =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->select(DB::raw('industry, count(id) as count'))
                        ->groupBy('industry')
                         ->orderByRaw('count DESC')
                        ->limit(3)
                        ->get();
     $leadsYTDIndustries = $leadsIndustry;
     $leadsYTDIndustriesData = [];
     if(count($leadsYTDIndustries)>0)
     {
      foreach($leadsYTDIndustries as $leadsYTDIndustry)
      {
        $title = $leadsYTDIndustry->industry==null?'No Industry':$leadsYTDIndustry->industry;
        array_push($leadsYTDIndustriesData,['industry'=>$title,'count'=>$leadsYTDIndustry->count]);
      }
     }

     //Query for Previous Day Solutions
     $leadsIndustry   =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->select(DB::raw('industry, count(id) as count'))
                        ->groupBy('industry')
                         ->orderByRaw('count DESC')
                        ->limit(3)
                        ->get();
     $previousDayLeadsIndustries  = $leadsIndustry;
     $previousDayLeadsIndustryData = [];
     if(count($previousDayLeadsIndustries)>0)
     {
      foreach($previousDayLeadsIndustries as $previousDayLeadsIndustry)
      {
        $title = $previousDayLeadsIndustry->industry==null?'No Industry':$previousDayLeadsIndustry->industry;
        array_push($previousDayLeadsIndustryData,['industry'=>$title,'count'=>$previousDayLeadsIndustry->count]);
      }
     }
     //Query for ToDay Solutions
     $leadsIndustry   =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->select(DB::raw('industry, count(id) as count'))
                        ->groupBy('industry')
                        ->orderByRaw('count DESC')
                        ->limit(3)
                        ->get();
     $leadsTodayIndustries  = $leadsIndustry;
     $leadsTodayIndustriesData = [];
     if(count($leadsTodayIndustries)>0)
     {
      foreach($leadsTodayIndustries as $leadsTodayIndustry)
      {
        $title = $leadsTodayIndustry->industry==null?'No Industry':$leadsTodayIndustry->industry;
        array_push($leadsTodayIndustriesData,['industry'=>$title,'count'=>$leadsTodayIndustry->count]);
      }
     }
     
     //Query for Previous Day Solutions
     $leadsIndustry   =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->select(DB::raw('solution_type, count(id) as count'))
                        ->groupBy('solution_type')
                         ->orderByRaw('count DESC')
                        ->limit(3)
                        ->get();
     $previousDayLeadsSolutions  = $leadsIndustry;
     $previousDayLeadsSolutionsData = [];
     if(count($previousDayLeadsSolutions)>0)
     {
      foreach($previousDayLeadsSolutions as $previousDayLeadsSolution)
      {
        $title = $previousDayLeadsSolution->solution_type==null?'No Solution Type':$previousDayLeadsSolution->solution_type;
        array_push($previousDayLeadsSolutionsData,['solution_type'=>$title,'count'=>$previousDayLeadsSolution->count]);
      }
     }
     //Query for ToDay Solutions
     $leadsIndustry   =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->select(DB::raw('solution_type, count(id) as count'))
                        ->groupBy('solution_type')
                        ->get();
     $leadsTodaySolutions  = $leadsIndustry;
     $leadsTodaySolutionsData = [];
     if(count($leadsTodaySolutions)>0)
     {
      foreach($leadsTodaySolutions as $leadsTodaySolution)
      {
        $title = $leadsTodaySolution->solution_type==null?'Unnamed Solution':$leadsTodaySolution->solution_type;
        array_push($leadsTodaySolutionsData,['solution_type'=>$title,'count'=>$leadsTodaySolution->count]);
      }
     }
     //Query for YTD Leads
     $leadsIndustry    =   Leads::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->select(DB::raw('solution_type, count(id) as count'))
                        ->groupBy('solution_type')
                        ->get();
     $leadsYTDSolutions = $leadsIndustry;
     $leadsYTDSolutions  = $leadsIndustry;
     $leadsYTDSolutionsData = [];
     if(count($leadsYTDSolutions)>0)
     {
      foreach($leadsYTDSolutions as $leadsYTDSolution)
      {
        $title = $leadsYTDSolution->solution_type==null?'Unnamed Solution':$leadsYTDSolution->solution_type;
        array_push($leadsYTDSolutionsData,['solution_type'=>$title,'count'=>$leadsYTDSolution->count]);
      }
     }
     //Query for Previous Day Visitors Category 
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where('page_type','=','category')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $previousDayCategoryVisitors  = $viewsVisitors;
     $previousDayCategoryVisitorsData = [];
     if(count($previousDayCategoryVisitors)>0)
     {
      foreach($previousDayCategoryVisitors as $previousDayCategoryVisitor)
      {
        $title = $previousDayCategoryVisitor->page_title==null?'Unnamed Category':$previousDayCategoryVisitor->page_title;
        array_push($previousDayCategoryVisitorsData,['solution_type'=>$title,'count'=>$previousDayCategoryVisitor->count]);
      }
     }
    
     //Query for ToDay Solutions
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where('page_type','=','category')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $todayCategoryVisitors  = $viewsVisitors;
     $todayCategoryVisitorsData = [];
     if(count($todayCategoryVisitors)>0)
     {
      foreach($todayCategoryVisitors as $todayCategoryVisitor)
      {
        $title = $todayCategoryVisitor->page_title==null?'Unnamed Category':$todayCategoryVisitor->page_title;
        array_push($todayCategoryVisitorsData,['solution_type'=>$title,'count'=>$todayCategoryVisitor->count]);
      }
     }
   
     //Query for YTD Leads
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where('page_type','=','category')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $ytdVisitors  = $viewsVisitors;
     $ytdVisitorsData = [];
     if(count($ytdVisitors)>0)
     {
      foreach($ytdVisitors as $ytdVisitor)
      {
        $title = $ytdVisitor->page_title==null?'Unnamed Category':$ytdVisitor->page_title;
        array_push($ytdVisitorsData,['solution_type'=>$title,'count'=>$ytdVisitor->count]);
      }
     }

     //Query for Previous Day Visitors Solutions 
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$yesterday")
                        ->where('page_type','=','solution')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $previousDaySolutionVisitors  = $viewsVisitors;
     $previousDaySolutionVisitorsData = [];
     if(count($previousDaySolutionVisitors)>0)
     {
      foreach($previousDaySolutionVisitors as $previousDaySolutionVisitor)
      {
        $title = $previousDaySolutionVisitor->page_title==null?'Unnamed Solution':$previousDaySolutionVisitor->page_title;
        array_push($previousDaySolutionVisitorsData,['solution_type'=>$title,'count'=>$previousDaySolutionVisitor->count]);
      }
     }
    
     //Query for ToDay  Visitors Solutions
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'=',"$today")
                        ->where('page_type','=','solution')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $todaySolutionVisitors  = $viewsVisitors;
     $todaySolutionVisitorsData = [];
     if(count($todaySolutionVisitors)>0)
     {
      foreach($todaySolutionVisitors as $todaySolutionVisitor)
      {
        $title = $todaySolutionVisitor->page_title==null?'Unnamed Solution':$todaySolutionVisitor->page_title;
        array_push($todaySolutionVisitorsData,['solution_type'=>$title,'count'=>$todaySolutionVisitor->count]);
      }
     }
   
     //Query for YTD  Visitors Solutions
     $viewsVisitors   =   Visitors::where(DB::raw("DATE_FORMAT(`created_at`,'%Y-%m-%d')"),'<=',"$today")
                        ->where('page_type','=','solution')
                        ->select(DB::raw('page_title, count(id) as count'))
                        ->groupBy('page_title')
                        ->orderByRaw('count DESC')
                        ->limit(5)
                        ->get();
     $ytdSolutionVisitors  = $viewsVisitors;
     $ytdSolutionVisitorsData = [];
     if(count($ytdSolutionVisitors)>0)
     {
      foreach($ytdSolutionVisitors as $ytdSolutionVisitor)
      {
        $title = $ytdSolutionVisitor->page_title==null?'Unnamed Solution':$ytdSolutionVisitor->page_title;
        array_push($ytdSolutionVisitorsData,['solution_type'=>$title,'count'=>$ytdSolutionVisitor->count]);
      }
     }
  
    $data = [];

    /*Sendy*/
    $dataSendy['api_key'] = 't2zlCcYUe2hquSclX1TL';
    
    $dataSendy['list_id'] = 'ofPfK4PiiNTHY1yB8KPHBw';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/api/subscribers/active-subscriber-count.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataSendy, '', '&'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec ($ch);
    curl_close ($ch);
    $data['landboatVendorNewsletter']                   = $response;
    
    $dataSendy['list_id'] = 'Cp585MJ6swvm7633wztnPNsw';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/api/subscribers/active-subscriber-count.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataSendy, '', '&'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec ($ch);
    curl_close ($ch);
    $data['landboatUserNewsletter']                     = $response;

    $dataSendy['list_id'] = 'KIbOluLHL7Yp763892b9HDeYHg';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/api/subscribers/active-subscriber-count.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataSendy, '', '&'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec ($ch);
    curl_close ($ch);
    $data['landboatVendor']                              = $response;

    $dataSendy['list_id'] = 'ftf7io0gn6UWTK763mYFhwEA';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/api/subscribers/active-subscriber-count.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataSendy, '', '&'));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec ($ch);
    curl_close ($ch);
    $data['landboatSubscriber']                          = $response;
    
    $data['previousDayMerchants']              = $previousDayMerchants;
    $data['merchantToday']                     = $merchantToday;
    $data['merchantYTD']                       = $merchantYTD; 
    $data['previousDayCustomer']               = $previousDayCustomer;
    $data['todayCustomer']                     = $todayCustomer;
    $data['customerYTD']                       = $customerYTD; 
    $data['previousDayPendingEmailMerchants']  = $previousDayPendingEmailMerchants;
    $data['merchantTodayPendingEmail']         = $merchantTodayPendingEmail;
    $data['merchantYTDPendingEmail']           = $merchantYTDPendingEmail;
    $data['previousDayPendingEmailCustomer']   = $previousDayPendingEmailCustomer;
    $data['customerTodayPendingEmail']         = $customerTodayPendingEmail;
    $data['customerYTDPendingEmail']           = $customerYTDPendingEmail; 
    $data['previousDaySolutions']              = $previousDaySolutions;
    $data['solutionsToday']                    = $solutionsToday;
    $data['solutionsYTD']                      = $solutionsYTD; 
    $data['previousDayLeads']                  = $previousDayLeads;
    $data['leadsToday']                        = $leadsToday;
    $data['leadsYTD']                          = $leadsYTD; 
    $data['previousDayLeadsIndustry']          = $previousDayLeadsIndustryData;
    $data['leadsTodayIndustry']                = $leadsTodayIndustriesData;
    $data['leadsYTDIndustry']                  = $leadsYTDIndustriesData; 
    $data['previousDayLeadsSolutions']         = $previousDayLeadsSolutionsData;
    $data['leadsTodaySolutions']               = $leadsTodaySolutionsData;
    $data['leadsYTDSolutions']                 = $leadsYTDSolutionsData; 
    $data['todayCategoryVisitor']              = $todayCategoryVisitorsData;
    $data['yesterdayCategoryVisitor']          = $previousDayCategoryVisitorsData;
    $data['ytdCategoryVisitor']                = $ytdVisitorsData;
    $data['todaySolutionVisitor']              = $todaySolutionVisitorsData;
    $data['yesterdaySolutionVisitor']          = $previousDaySolutionVisitorsData;
    $data['ytdSolutionVisitor']                = $ytdSolutionVisitorsData;
    ?>
    <style>
    thead tr{
      background: #000;
      color: #fff;
      font-family: sans-serif;
      padding: 14px;
    }
    th,td {
      padding: 10px;
    }

    </style>  
      <table border="1">
        <thead>
          <tr style="background: #000; color: #fff; font-family: sans-serif;">
            <th>KPI</th>
            <th>Today</th>
            <th>Yesterday</th>
            <th>YTD</th>
          </tr>  
        </thead>
        <tbody>
          <tr>
            <td>User Signup Today</td>
            <td><?=$data['todayCustomer']?></td>
            <td><?=$data['previousDayCustomer']?></td>
            <td><?=$data['customerYTD']?></td>
          </tr> 
          <tr>
            <td>Vendor Signup Today</td>
            <td><?=$data['merchantToday']?></td>
            <td><?=$data['previousDayMerchants']?></td>
            <td><?=$data['merchantYTD']?></td>
          </tr>
          <tr>
            <td>User Pending Email Verification</td>
            <td><?=$data['customerTodayPendingEmail']?></td>
            <td><?=$data['previousDayPendingEmailCustomer']?></td>
            <td><?=$data['customerYTDPendingEmail']?></td>
          </tr>
          <tr>
            <td>Vendor Pending Email Verification</td>
            <td><?=$data['merchantTodayPendingEmail']?></td>
            <td><?=$data['previousDayPendingEmailMerchants']?></td>
            <td><?=$data['merchantYTDPendingEmail']?></td>
          </tr>
          <tr>
            <td>Solutions on Platform (Registered)</td>
            <td><?=$data['solutionsToday']?></td>
            <td><?=$data['previousDaySolutions']?></td>
            <td><?=$data['solutionsYTD']?></td>
          </tr>
          <tr>
            <td>Leads Received</td>
            <td><?=json_encode($data['leadsToday'])?></td>
            <td><?=json_encode($data['previousDayLeads'])?></td>
            <td><?=json_encode($data['leadsYTD'])?></td>
          </tr>
          <tr>
            <td>Leads (By Industry) Received</td>
            <td><?php $leadsTodayIndustries = $data['leadsTodayIndustry'];
                       foreach($leadsTodayIndustries as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['industry'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach;                ?>
            </td>

            <td><?php $previousDayLeadsIndustries = $data['previousDayLeadsIndustry'];
                   foreach($previousDayLeadsIndustries as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['industry'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
               ?>
            </td>
            <td>
              <?php $leadYTDIndustries = $data['leadsYTDIndustry'];
                       foreach($leadYTDIndustries as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['industry'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
                 ?>
            </td>
          </tr>
          <tr>
            <td>Leads (By Solutions) Received</td>
            <td><?php $previousDayLeadsSolutions =  $data['previousDayLeadsSolutions'];
                      foreach($previousDayLeadsSolutions as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
               ?>
            </td>
            <td><?php $leadsTodaySolutions = $data['leadsTodaySolutions'];
                      foreach($leadsTodaySolutions as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
               ?>
            </td>
            <td><?php $leadsYTDSolutions = $data['leadsYTDSolutions'];
                      foreach($leadsYTDSolutions as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
            
               ?>
            </td>
          </tr>
          <tr>
            <td>Visitors By Category</td>
            <td><?php $todayCategoryVisitors = $data['todayCategoryVisitor']; 
                    foreach($todayCategoryVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
               ?>
            </td>
            <td><?php $yesterdayCategoryVisitors = $data['yesterdayCategoryVisitor'];
                    foreach($yesterdayCategoryVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                       endforeach; 
            
               ?>
            </td>
            <td><?php $ytdCategoryVisitors = $data['ytdCategoryVisitor'];
                    foreach($ytdCategoryVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                    endforeach; 
            
               ?>
            </td>
          </tr>
          <tr>
            <td>Visitors By Solutions</td>
            <td><?php $todaySolutionVisitors = $data['todaySolutionVisitor'];
                foreach($todaySolutionVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                    endforeach; 
            
              ?></td>
            <td><?php $yesterdaySolutionVisitors = $data['yesterdaySolutionVisitor'];
                foreach($yesterdaySolutionVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                    endforeach; 
              ?></td>
            <td><?php $ytdSolutionVisitors = $data['ytdSolutionVisitor'];
                foreach($ytdSolutionVisitors as $key=>$leadYTDIndustry):
                        echo '<strong>'.$leadYTDIndustry['solution_type'].' - </strong>'.$leadYTDIndustry['count'].'<br/>';  
                    endforeach; 
            
              ?></td>
          </tr>
        </tbody>
      </table>
    <?php 
    }

}
?>

