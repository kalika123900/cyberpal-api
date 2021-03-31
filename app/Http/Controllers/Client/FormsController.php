<?php

namespace App\Http\Controllers\Client;

use App\Contacts;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreContactFormRequest;
use App\Http\Resources\Contacts as ContactsResource;
use App\Leads;
use Illuminate\Http\Request;
use App\User;
use App\Notifications\SolutionEnquirySubmitted;

class FormsController extends Controller
{
    public function addNewContactFormData (StoreContactFormRequest $request) {
      
        try {
            $requestData = $request->all();
            $requestData['name']   = $request->first_name." ".$request->last_name;
            $data = new Contacts($requestData);
            $data->save();
            return new ContactsResource($data);
        } catch(\Exception $e){
            return $e->getMessage(); 
        } 
    }

    public function addNewRequestSeriviceData (Request $request) {
        try {
 
                $user = auth('api')->user();
                $requestData = $request->all();
                $products = $requestData['solution_id'];
                foreach( $products as $key=>$map)
                {   $requestData['requestNeeded'] = implode(',',$requestData['subject']);
                    $requestData['budget'] = $request->budget_from."-".$request->budget_to;
                    $requestData['solution_id'] = [$map];
                    $data = new Leads($requestData);
                    $data->save();
                }
                if(!empty($user)) {
                        $loggedInUser = User::where('id', $user->id)->first();
                        $loggedInUser->notify(new SolutionEnquirySubmitted($loggedInUser));
                }

                return new ContactsResource($data);
        } catch(\Exception $e){
            return $e->getMessage(); 
        }  
    }
}
