<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CustomerLoginRequest;
use App\Http\Requests\Client\CustomerRegisterRequest;
use App\Notifications\CustomerSignupActivate;
use App\Notifications\CustomerSignUpCompleted;
use App\Notifications\CustomerRegistrationAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;

class AuthController extends Controller
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
            
             return $response;
        }
    }

    private function issueToken (User $user) 
    {
        $userToken = $user->token() ?? $user->createToken('socialLogin');

        return [
            "token_type" => "Bearer",
            "access_token" => $userToken->accessToken
        ];
    }
    
    public function register (CustomerRegisterRequest $request)
    {
        do {
            $uid = mt_rand(1000000000, 9999999999);
        } while (User::where('uid', $uid)->exists());

        $this->createCometChatUser($request, $uid);

        $user = new User([
            'name' => $request->name,
            'uid' => $uid,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'isVerified' => 0, // - FOR ENABLING & DISABLING USER.
            'isActive' => 1, 
            'user_type' => 'customer',
            'activation_token' => Str::random(60)
        ]);

        $data = [];
        $data['api_key'] = 't2zlCcYUe2hquSclX1TL';
        $data['name']    = $request->name;
        $data['email']   = $request->email;
        $data['list']    = 'HoFTw4OonZgqmG892JqrNeRQ';
        $data['boolean'] = 'true';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.cyberpal.tech/sendy/subscribe");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Cache-Control: no-cache'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close ($ch);

        $user->save();
        $user->notify(new CustomerSignupActivate($user));

        $admin = User::find(1);
            
        $admin->notify(new CustomerRegistrationAdmin($admin,$user));

        return response()->json([
            'message' => 'Please check your mail and verify account.',
        ], 200);
    }
    
    public function verifyRegister ($token) {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Activation token is not valid. Please contact support.'
            ], 404);
        }
        $user->isVerified = 1;
        $user->activation_token = '';
        $user->update();
        
        $user->notify(new CustomerSignUpCompleted($user));

        return $user;
    }

    public function login (CustomerLoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        try {
            if (auth()->attempt($credentials)) {
                $user = auth()->user();
                
                if (!empty($user->user_type) && $user->user_type === 'customer') {
                    if ($user->isVerified === 1) {
                        $token = auth()->user()->createToken($request->email)->accessToken;
                    
                        return response()->json([
                            'token' => $token,
                            'message' => 'Account Logged In.',
                        ], 200);
                    } else throw new \Exception("Your account is not yet verified");
                } else throw new \Exception("You are not a customer.");
            } else throw new \Exception("Email & password not matched.");
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 422);
        }
    }
    
    public function google (Request $request) {
        $provider = 'google';

        $checkIfAccountExist = User::where([
            "provider_id" => $request->provider_id,
            "provider" => $provider
        ])->first();

        if ($checkIfAccountExist) {
            if ($checkIfAccountExist->user_type === "customer") {
                return $this->issueToken($checkIfAccountExist);
            } else throw new \Exception("You are not a customer");
        } else {
            $checkIfAlreadyHaveAccount = User::where("email", $request->email)->first();

            if ($checkIfAlreadyHaveAccount) {
                if ($checkIfAlreadyHaveAccount->user_type === "customer") {
                    $checkIfAlreadyHaveAccount->provider_id = $request->provider_id;
                    $checkIfAlreadyHaveAccount->provider = $provider;
                    $checkIfAlreadyHaveAccount->isVerified = 1;
                    $checkIfAlreadyHaveAccount->update();

                    return $this->issueToken($checkIfAlreadyHaveAccount);
                } else throw new \Exception("You are not a customer");
            } else {
                do {
                    $uid = mt_rand(1000000000, 9999999999);
                } while (User::where('uid', $uid)->exists());

                $this->createCometChatUser($request, $uid);

                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = 'customer';
                $user->uid = $uid;
                $user->profile_picture = $request->profile_picture;
                $user->provider_id = $request->provider_id;
                $user->provider = $request->provider;
                $user->isVerified = 1;
                $user->save();

                return $this->issueToken($user);
            }
        }
    }

    public function linkedin (Request $request) {
        $provider = 'linkedin';

        $checkIfAccountExist = User::where([
            "provider_id" => $request->provider_id,
            "provider" => $provider
        ])->first();
        
        if ($checkIfAccountExist) {
            if ($checkIfAccountExist->user_type === "customer") {
                return $this->issueToken($checkIfAccountExist);
            } else throw new \Exception("You are not a customer");
        } else {
            $uri = env("LINKEDIN_REDIRECT_URI", "http://localhost:3000/linkedin");
            $secret = env("LINKEDIN_CLIENT_SECRET", "tOjZIMNF6jiA836l");;
            $id = env("LINKEDIN_CLIENT_ID", "86b3ingqrtcw1l");;

            $url = 'https://www.linkedin.com/oauth/v2/accessToken?grant_type=authorization_code&redirect_uri='.$uri.'&client_id='.$id.'&client_secret='.$secret.'&code='.$request->provider_id;
            
            $client = new \GuzzleHttp\Client();
            $request1 = $client->get($url);
            $response1 = $request1->getBody()->getContents();
            $linkedinAuthResponse = (array)json_decode($response1);

            if ($linkedinAuthResponse['access_token']) {
                $token = 'Bearer '.$linkedinAuthResponse['access_token'];
                $header = array('Authorization'=> $token);
                // $emailURL = 'https://api.linkedin.com/v2/clientAwareMemberHandles?q=members&projection=(elements*(primary,type,handle~))';
                $emailURL = 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))';

                $client2 = new \GuzzleHttp\Client();
                $request2 = $client2->get($emailURL, array('headers' => $header));
                $response2 = $request2->getBody()->getContents();
                $linkedInEmailJSON = (array)json_decode($response2, true);
                $linkedInEmail =  $linkedInEmailJSON['elements'][0]['handle~']['emailAddress'];

                if ($linkedInEmail) {
                    $checkIfAlreadyHaveAccount = User::where("email", $linkedInEmail)->first();
                    
                    if ($checkIfAlreadyHaveAccount) {
                        if ($checkIfAlreadyHaveAccount->user_type === "customer") {
                            $checkIfAlreadyHaveAccount->provider_id = $request->provider_id;
                            $checkIfAlreadyHaveAccount->provider = $provider;
                            $checkIfAlreadyHaveAccount->isVerified = 1;
                            $checkIfAlreadyHaveAccount->update();

                            return $this->issueToken($checkIfAlreadyHaveAccount);
                        } else throw new \Exception("You are not a customer");
                    } else {
                        $profileURL = 'https://api.linkedin.com/v2/me';
                        // $profileURL = "https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))";
                        // - For getting profile picture;

                        $client3 = new \GuzzleHttp\Client();
                        $request3 = $client3->get($profileURL, array('headers' => $header));
                        $response3 = $request3->getBody()->getContents();
                        $linkedInBasicInfo = (array)json_decode($response3);

                        do {
                            $uid = mt_rand(1000000000, 9999999999);
                        } while (User::where('uid', $uid)->exists());

                        $this->createCometChatUser($request, $uid);
                        
                        $user = new User;
                        $user->name = $linkedInBasicInfo['localizedFirstName'] .' '. $linkedInBasicInfo['localizedLastName'];
                        $user->email = $linkedInEmail;
                        $user->user_type = 'customer';
                        $user->uid = $uid;
                        // $user->profile_picture = $request->profile_picture;
                        $user->provider_id = $request->provider_id;
                        $user->provider = $request->provider;
                        $user->isVerified = 1;
                        $user->save();

                        return $this->issueToken($user);
                    }
                }
            }
        }
    }

    public function getUser ()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
  
    // - # TODO 
    public function updateLoggedInUser (Request $request) {
        try {
            $id = auth()->user()->id;
            User::where('id', $id)->update($request->all());
            return response()->json(['message' => 'Profile updated successfully.'], 200);
        } catch (\Exception $err) {
            return response()->json(['message' => $err->getMessage()], 401);
        }
    }
    function getlocale(Request $request)
    {
      
        $ip = $_SERVER['REMOTE_ADDR'];

        $countryCurrency = json_decode('{"BD": "BDT", "BE": "EUR", "BF": "XOF", "BG": "BGN", "BA": "BAM", "BB": "BBD", "WF": "XPF", "BL": "EUR", "BM": "BMD", "BN": "BND", "BO": "BOB", "BH": "BHD", "BI": "BIF", "BJ": "XOF", "BT": "BTN", "JM": "JMD", "BV": "NOK", "BW": "BWP", "WS": "WST", "BQ": "USD", "BR": "BRL", "BS": "BSD", "JE": "GBP", "BY": "BYR", "BZ": "BZD", "RU": "RUB", "RW": "RWF", "RS": "RSD", "TL": "USD", "RE": "EUR", "TM": "TMT", "TJ": "TJS", "RO": "RON", "TK": "NZD", "GW": "XOF", "GU": "USD", "GT": "GTQ", "GS": "GBP", "GR": "EUR", "GQ": "XAF", "GP": "EUR", "JP": "JPY", "GY": "GYD", "GG": "GBP", "GF": "EUR", "GE": "GEL", "GD": "XCD", "GB": "GBP", "GA": "XAF", "SV": "USD", "GN": "GNF", "GM": "GMD", "GL": "DKK", "GI": "GIP", "GH": "GHS", "OM": "OMR", "TN": "TND", "JO": "JOD", "HR": "HRK", "HT": "HTG", "HU": "HUF", "HK": "HKD", "HN": "HNL", "HM": "AUD", "VE": "VEF", "PR": "USD", "PS": "ILS", "PW": "USD", "PT": "EUR", "SJ": "NOK", "PY": "PYG", "IQ": "IQD", "PA": "PAB", "PF": "XPF", "PG": "PGK", "PE": "PEN", "PK": "PKR", "PH": "PHP", "PN": "NZD", "PL": "PLN", "PM": "EUR", "ZM": "ZMK", "EH": "MAD", "EE": "EUR", "EG": "EGP", "ZA": "ZAR", "EC": "USD", "IT": "EUR", "VN": "VND", "SB": "SBD", "ET": "ETB", "SO": "SOS", "ZW": "ZWL", "SA": "SAR", "ES": "EUR", "ER": "ERN", "ME": "EUR", "MD": "MDL", "MG": "MGA", "MF": "EUR", "MA": "MAD", "MC": "EUR", "UZ": "UZS", "MM": "MMK", "ML": "XOF", "MO": "MOP", "MN": "MNT", "MH": "USD", "MK": "MKD", "MU": "MUR", "MT": "EUR", "MW": "MWK", "MV": "MVR", "MQ": "EUR", "MP": "USD", "MS": "XCD", "MR": "MRO", "IM": "GBP", "UG": "UGX", "TZ": "TZS", "MY": "MYR", "MX": "MXN", "IL": "ILS", "FR": "EUR", "IO": "USD", "SH": "SHP", "FI": "EUR", "FJ": "FJD", "FK": "FKP", "FM": "USD", "FO": "DKK", "NI": "NIO", "NL": "EUR", "NO": "NOK", "NA": "NAD", "VU": "VUV", "NC": "XPF", "NE": "XOF", "NF": "AUD", "NG": "NGN", "NZ": "NZD", "NP": "NPR", "NR": "AUD", "NU": "NZD", "CK": "NZD", "XK": "EUR", "CI": "XOF", "CH": "CHF", "CO": "COP", "CN": "CNY", "CM": "XAF", "CL": "CLP", "CC": "AUD", "CA": "CAD", "CG": "XAF", "CF": "XAF", "CD": "CDF", "CZ": "CZK", "CY": "EUR", "CX": "AUD", "CR": "CRC", "CW": "ANG", "CV": "CVE", "CU": "CUP", "SZ": "SZL", "SY": "SYP", "SX": "ANG", "KG": "KGS", "KE": "KES", "SS": "SSP", "SR": "SRD", "KI": "AUD", "KH": "KHR", "KN": "XCD", "KM": "KMF", "ST": "STD", "SK": "EUR", "KR": "KRW", "SI": "EUR", "KP": "KPW", "KW": "KWD", "SN": "XOF", "SM": "EUR", "SL": "SLL", "SC": "SCR", "KZ": "KZT", "KY": "KYD", "SG": "SGD", "SE": "SEK", "SD": "SDG", "DO": "DOP", "DM": "XCD", "DJ": "DJF", "DK": "DKK", "VG": "USD", "DE": "EUR", "YE": "YER", "DZ": "DZD", "US": "USD", "UY": "UYU", "YT": "EUR", "UM": "USD", "LB": "LBP", "LC": "XCD", "LA": "LAK", "TV": "AUD", "TW": "TWD", "TT": "TTD", "TR": "TRY", "LK": "LKR", "LI": "CHF", "LV": "EUR", "TO": "TOP", "LT": "LTL", "LU": "EUR", "LR": "LRD", "LS": "LSL", "TH": "THB", "TF": "EUR", "TG": "XOF", "TD": "XAF", "TC": "USD", "LY": "LYD", "VA": "EUR", "VC": "XCD", "AE": "AED", "AD": "EUR", "AG": "XCD", "AF": "AFN", "AI": "XCD", "VI": "USD", "IS": "ISK", "IR": "IRR", "AM": "AMD", "AL": "ALL", "AO": "AOA", "AQ": "", "AS": "USD", "AR": "ARS", "AU": "AUD", "AT": "EUR", "AW": "AWG", "IN": "INR", "AX": "EUR", "AZ": "AZN", "IE": "EUR", "ID": "IDR", "UA": "UAH", "QA": "QAR", "MZ": "MZN"}',true);

        $jsonArrayResponse = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
     
        $countryCode =  $jsonArrayResponse['geoplugin_countryCode'];

        $conversionRate = $jsonArrayResponse['geoplugin_currencyConverter'];
        $currency = $jsonArrayResponse['geoplugin_currencyCode'];
        $response = [];
        $response['message'] = 'Data gathered successfully!';
        $response['conversion'] = $conversionRate;
        $response['countrycode'] = $countryCode;
        $response['currency'] = $currency;
        return response()->json($response, 200);
    }
    function getConversionRate(Request $request)
    {
        $countryCurrency = json_decode('{"BD": "BDT", "BE": "EUR", "BF": "XOF", "BG": "BGN", "BA": "BAM", "BB": "BBD", "WF": "XPF", "BL": "EUR", "BM": "BMD", "BN": "BND", "BO": "BOB", "BH": "BHD", "BI": "BIF", "BJ": "XOF", "BT": "BTN", "JM": "JMD", "BV": "NOK", "BW": "BWP", "WS": "WST", "BQ": "USD", "BR": "BRL", "BS": "BSD", "JE": "GBP", "BY": "BYR", "BZ": "BZD", "RU": "RUB", "RW": "RWF", "RS": "RSD", "TL": "USD", "RE": "EUR", "TM": "TMT", "TJ": "TJS", "RO": "RON", "TK": "NZD", "GW": "XOF", "GU": "USD", "GT": "GTQ", "GS": "GBP", "GR": "EUR", "GQ": "XAF", "GP": "EUR", "JP": "JPY", "GY": "GYD", "GG": "GBP", "GF": "EUR", "GE": "GEL", "GD": "XCD", "GB": "GBP", "GA": "XAF", "SV": "USD", "GN": "GNF", "GM": "GMD", "GL": "DKK", "GI": "GIP", "GH": "GHS", "OM": "OMR", "TN": "TND", "JO": "JOD", "HR": "HRK", "HT": "HTG", "HU": "HUF", "HK": "HKD", "HN": "HNL", "HM": "AUD", "VE": "VEF", "PR": "USD", "PS": "ILS", "PW": "USD", "PT": "EUR", "SJ": "NOK", "PY": "PYG", "IQ": "IQD", "PA": "PAB", "PF": "XPF", "PG": "PGK", "PE": "PEN", "PK": "PKR", "PH": "PHP", "PN": "NZD", "PL": "PLN", "PM": "EUR", "ZM": "ZMK", "EH": "MAD", "EE": "EUR", "EG": "EGP", "ZA": "ZAR", "EC": "USD", "IT": "EUR", "VN": "VND", "SB": "SBD", "ET": "ETB", "SO": "SOS", "ZW": "ZWL", "SA": "SAR", "ES": "EUR", "ER": "ERN", "ME": "EUR", "MD": "MDL", "MG": "MGA", "MF": "EUR", "MA": "MAD", "MC": "EUR", "UZ": "UZS", "MM": "MMK", "ML": "XOF", "MO": "MOP", "MN": "MNT", "MH": "USD", "MK": "MKD", "MU": "MUR", "MT": "EUR", "MW": "MWK", "MV": "MVR", "MQ": "EUR", "MP": "USD", "MS": "XCD", "MR": "MRO", "IM": "GBP", "UG": "UGX", "TZ": "TZS", "MY": "MYR", "MX": "MXN", "IL": "ILS", "FR": "EUR", "IO": "USD", "SH": "SHP", "FI": "EUR", "FJ": "FJD", "FK": "FKP", "FM": "USD", "FO": "DKK", "NI": "NIO", "NL": "EUR", "NO": "NOK", "NA": "NAD", "VU": "VUV", "NC": "XPF", "NE": "XOF", "NF": "AUD", "NG": "NGN", "NZ": "NZD", "NP": "NPR", "NR": "AUD", "NU": "NZD", "CK": "NZD", "XK": "EUR", "CI": "XOF", "CH": "CHF", "CO": "COP", "CN": "CNY", "CM": "XAF", "CL": "CLP", "CC": "AUD", "CA": "CAD", "CG": "XAF", "CF": "XAF", "CD": "CDF", "CZ": "CZK", "CY": "EUR", "CX": "AUD", "CR": "CRC", "CW": "ANG", "CV": "CVE", "CU": "CUP", "SZ": "SZL", "SY": "SYP", "SX": "ANG", "KG": "KGS", "KE": "KES", "SS": "SSP", "SR": "SRD", "KI": "AUD", "KH": "KHR", "KN": "XCD", "KM": "KMF", "ST": "STD", "SK": "EUR", "KR": "KRW", "SI": "EUR", "KP": "KPW", "KW": "KWD", "SN": "XOF", "SM": "EUR", "SL": "SLL", "SC": "SCR", "KZ": "KZT", "KY": "KYD", "SG": "SGD", "SE": "SEK", "SD": "SDG", "DO": "DOP", "DM": "XCD", "DJ": "DJF", "DK": "DKK", "VG": "USD", "DE": "EUR", "YE": "YER", "DZ": "DZD", "US": "USD", "UY": "UYU", "YT": "EUR", "UM": "USD", "LB": "LBP", "LC": "XCD", "LA": "LAK", "TV": "AUD", "TW": "TWD", "TT": "TTD", "TR": "TRY", "LK": "LKR", "LI": "CHF", "LV": "EUR", "TO": "TOP", "LT": "LTL", "LU": "EUR", "LR": "LRD", "LS": "LSL", "TH": "THB", "TF": "EUR", "TG": "XOF", "TD": "XAF", "TC": "USD", "LY": "LYD", "VA": "EUR", "VC": "XCD", "AE": "AED", "AD": "EUR", "AG": "XCD", "AF": "AFN", "AI": "XCD", "VI": "USD", "IS": "ISK", "IR": "IRR", "AM": "AMD", "AL": "ALL", "AO": "AOA", "AQ": "", "AS": "USD", "AR": "ARS", "AU": "AUD", "AT": "EUR", "AW": "AWG", "IN": "INR", "AX": "EUR", "AZ": "AZN", "IE": "EUR", "ID": "IDR", "UA": "UAH", "QA": "QAR", "MZ": "MZN"}',true);

        $countryCode =  $request->country_code;

        $currency = $countryCurrency[$countryCode]; 

        $key = 'q5X9SthQsM6CmGkTtzhX2WS7Tpc2XB';

        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, "https://www.amdoren.com/api/currency.php?api_key=$key&from=USD&to=$currency");

        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $amountConverted = curl_exec($cURLConnection);

        curl_close($cURLConnection);

        $amountArray = json_decode($amountConverted);
        $response = [];
        $response['message'] = 'Data gathered successfully!';
        $response['conversion'] = $amountArray;
        $response['countrycode'] = $countryCode;
        $response['currency'] = $currency;
        return response()->json($response, 200);
    }
}
