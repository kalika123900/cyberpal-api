<?php
namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;

Class CompanyController extends Controller{

  public function getCompany(Request $request){
       try {
            $user = auth()->user();

            if (!empty($user) && $user->user_type == "merchant") {
                
                $data = Company::where('vendor_id',$user->id)->get()->first();
                if($data!='')
                {
                  return response()->json($data, 200);
                }
                else
                {
                  throw new \Exception(("You are not allowed to perform this operation."));
                }
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
  }
  public function updateCompany(Request $request){
      try {      
        $user = auth()->user();

        $data = $request->all();
        $company = Company::where('vendor_id',$user->id)->get()->first();
        
        foreach($data as $key=>$value)
        {
          if($key=='vendor_id')
          continue;

          $company[$key] = $data[$key];
        }
        $company->save();
        return response()->json($company, 200);
      }
      catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
  }
}