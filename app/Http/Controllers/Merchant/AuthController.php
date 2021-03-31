<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUpdateRequest;
use Illuminate\Http\Request;
use App\User;
use App\Merchants;
use App\Notifications\MerchantPasswordResetRequest;
use App\Notifications\MerchantPasswordResetSuccess;
use App\Notifications\MerchantSignUpCompleted;
use App\Notifications\ExpertSignUpCompleted;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\PasswordReset;
use App\Http\Requests\Merchant\ProfileUpdateRequest;

class AuthController extends Controller
{
    // - # FINALISED
    public function updateLoggedInUser (ProfileUpdateRequest $request) {
        try {
            $user = User::where('id', auth()->user()->id)->first();
            $user->update($request->except('id', 'merchant', 'email'));
            // #TODO: Update ComentChat User
            if (!empty($request->merchant)) {
                $merchant = Merchants::where('user_id', auth()->user()->id)->first();
                $merchant->update($request->merchant);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // - # FINALISED
    public function updateAvatar (AvatarUpdateRequest $request) {
        try {
            $user = User::where('id', auth()->user()->id)->first();
            $user->update($request->only('profile_picture'));
            // #TODO: Update ComentChat Picture
            return response()->json($user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function verifyRegister ($token) {
        $user = User::where('activation_token', $token)->with('merchant')->first();

        if (!$user) {
            return response()->json([
                'message' => 'Activation token is not valid. Please contact support.'
            ], 404);
        }

        if ($user->user_type !== "merchant") {
            return response()->json([
                'message' => 'You are not a customer.'
            ], 404);
        }

        $user->isVerified = 1;
        $user->activation_token = '';
        $user->update();
        
        if ($user->merchant) {
            if ($user->merchant->vendor_type === "expert") {
                $user->notify(new ExpertSignUpCompleted($user));
            } else {
                $user->notify(new MerchantSignUpCompleted($user));
            }
        }

        return $user;
    }

    public function login (Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        try {
            if (auth()->attempt($credentials)) {
                $user = auth()->user();
                
                if (!empty($user->user_type) && $user->user_type === "merchant") {
                    if ($user->isVerified === 1) {
                        $token = auth()->user()->createToken($request->email)->accessToken;

                        return response()->json([
                            'token' => $token,
                            'message' => 'Account Logged In.',
                        ], 200);
                    } else throw new \Exception("Your account is not yet verified");
                } else throw new \Exception('You are not a merchant');
            } else throw new \Exception("Email & password not matched.");
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function getLoggedInUser () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $user = User::where('id', $user->id)->with('merchant', 'merchant.category')->first();
                return response()->json($user, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
		]);
		
		$user = User::where('email', $request->email)->first();
		
        if (!$user) {
            return response()->json([
                'message' => 'We can\'t find a user with this e-mail address.'
			], 404);
        }
        
        if ($user->user_type !== "merchant") {
            return response()->json([
                'message' => 'You\'re not a partner.'
			], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
             ]
		);
		
        if ($user && $passwordReset) {
            $user->notify(
                new MerchantPasswordResetRequest($passwordReset->token, $user->email)
			);
		}

        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
			->first();
			
        if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
			], 404);
		}
			
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
			$passwordReset->delete();
			
            return response()->json([
                'message' => 'This password reset has been expired. Please create a new request.'
            ], 404);
		}
		
        return response()->json($passwordReset);
    }
     /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'token' => 'required|string'
		]);
		
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
		
		if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
			], 404);
		}

        $user = User::where('email', $passwordReset->email)->first();
 
		if (!$user) {
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'
			], 404);
		}

        $user->password = bcrypt($request->password);
		$user->save();
		
		$passwordReset->delete();
		
		$user->notify(new MerchantPasswordResetSuccess($passwordReset));
		
        return response()->json($user);
    }
}
