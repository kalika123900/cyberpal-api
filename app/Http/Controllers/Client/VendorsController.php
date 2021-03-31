<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreVendorsSignupRequest;
use App\Http\Resources\Vendors as ResourcesVendors;
use App\Merchants;
use App\User;
use Illuminate\Http\Request;
use App\Notifications\MerchantSignUpRequest;
use App\Notifications\VendorRegistrationAdmin;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    public function createCometChatUser ($user, $uid) {
        $headers = [
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'apikey' => env("COMETCHAT_APIKEY"),
            'appid' => env("COMETCHAT_APPID"),
        ];

        $client = new \GuzzleHttp\Client([
            'headers' => $headers
        ]);
        
        try {
            $request = $client->request('POST', 'https://api-'.env("COMETCHAT_REGION").'.cometchat.io/v2.0/users', [
                'json' => [
                    'uid' => $uid,
                    'name' => $user->name,
                    'role' => 'customer',
                ]
            ]);

            $response = $request->getBody()->getContents();
            $data = (array)json_decode($response);

            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
             $response = $e->getResponse();
            // var_dump($response->getReasonPhrase()); // Response message;
             return $response->getBody();
        }
    }

    public function addNewVendor (StoreVendorsSignupRequest $request) {
        try {
            do {
                $uid = mt_rand(1000000000, 9999999999);
            } while (User::where('uid', $uid)->exists());

            $data = [];
            $data['api_key'] = 't2zlCcYUe2hquSclX1TL';
            $data['name']  = $request->name;
            $data['email']   = $request->email;
            $data['list'] = 'fboci84pDRmfKU9Z9kEkaA';
            $data['boolean'] = 'true';
           
            //$this->createCometChatUser($request, $uid);

            $request->merge([
                'password' => bcrypt($request->password),
                'user_type' => 'merchant',
                'isVerified' => 0,
                'isActive' => 1,
                'activation_token' => Str::random(60),
                'uid' => $uid
            ]);

            // - Creating User
            $user = User::create($request->only(
                'name', 'email', 'phone', 'password', 'user_type', 'isVerified', 'activation_token', 'organisation_name', 
                'organisation_size', 'organisation_url', 'organisational_role', 'industry', 'skills', 'uid'
            ));
            $request->merge([
                'user_id' => $user->id,
            ]);

            // $user->assignRole('Vendor');
            
            // - Creating Partner
            $partner = Merchants::create($request->except(
                'name', 'email', 'phone', 'password', 'user_type', 'isVerified', 'activation_token', 'organisation_name', 
                'organisation_size', 'organisation_url', 'organisational_role', 'industry'
            ));      

            $user->notify(new MerchantSignUpRequest($user));
            
            $admin = User::find(1);
            
            $admin->notify(new VendorRegistrationAdmin($admin,$user));

            $data = [];
            $data['api_key'] = 't2zlCcYUe2hquSclX1TL';
            $data['name']  = $request->name;
            $data['email']   = $request->email;
            $data['list'] = 'fboci84pDRmfKU9Z9kEkaA';
            $data['boolean'] = 'true';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/subscribe");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec ($ch);
            curl_close ($ch);
            return new ResourcesVendors($partner);
        } catch (\Exception $e) {
            // - Remove User or Merchant if created.
            return $e->getMessage();
        }
    }
}
