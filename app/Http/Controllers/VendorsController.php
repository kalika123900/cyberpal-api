<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorsSignupRequest;
use Illuminate\Http\Request;
use App\Merchants;
use App\User;

class VendorsController extends Controller
{    
    public function index(Request $request)
    {
        try { 
            if (!empty($request->role)) {
                if (!empty($request->role === "customer")) { 
                    $users = User::where('user_type', $request->role)->paginate(30);
                    return response()->json($users, 200);
                } else {
                    $users = User::whereHas('merchant', function ($q) use ($request) {
                        $q->where('vendor_type', $request->role);
                    })->orderBy(
                        'created_at', 'DESC'
                    )->paginate(30);

                    return response()->json($users, 200);
                }
            } else return response()->json('Operation not found', 404);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }
    }

    public function store()
    {
        return response()->json('Operation not found', 404);
    }

    public function show($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if ($user->user_type === 'merchant') {
                $user['merchant'] = $user->merchant;
                $user['category'] = $user->merchant->category;
            }

            return response()->json([
                'message' => 'Successfully fetched merchants.',
                'data' => $user
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->only('name', 'phone', 'organisation_name', 'organisation_url', 'organisation_role', 'organisation_size', 'industry'));

            if ($user->user_type === "merchant") {
                $merchant = Merchants::where('user_id', $id)->first();
                $merchant->update($request->only('business_name', 'business_address', 'position', 'organisation_size', 'message', 'linked_url', 'twitter_url', 'facebook_url', 'youtube_url', 'instagram_url', 'website_url'));
            }

            return response()->json([
                'message' => 'Successfully updated merchants.',
                'data' => $user
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 400);
        }
    }

    // - Done
    public function destroy()
    {
        return response()->json('Operation not found', 404);
    }

    public function searchData(Request $request) {
        try { 
            if (!empty($request->role === "customer")) { 
                $users = User::where(
                    'name', 'LIKE', '%'.$request->q.'%'
                )->where('user_type', $request->role)->paginate(30);

                return response()->json($users, 200);
            } else {
                $users = User::where(
                    'name', 'LIKE', '%'.$request->q.'%'
                )->whereHas('merchant', function ($q) use ($request) {
                    $q->where('vendor_type', $request->role);
                })->orderBy(
                    'created_at', 'DESC'
                )->paginate(30);

                return response()->json($users, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    // - Done
    public function enableAccount (Request $request) {
        try {
            $user = User::findOrFail($request->id);
            $user->isVerified = 1;
            $user->save();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }  
    }

    // - Done
    public function disableAccount (Request $request) {
        try {
            $user = User::findOrFail($request->id);
            $user->isVerified = 0;
            $user->save();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            return response()->json($err->getMessage(), 400);
        }        
    }
}
