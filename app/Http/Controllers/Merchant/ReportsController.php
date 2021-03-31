<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class ReportsController extends Controller
{
    public function getExpertReport () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $user = User::where('id', $user->id)->first();

                return response()->json([
                    'total' => $user->getLeadsCountAttribute(),
                    'done' =>  $user->getCompletedLeadsCountAttribute(),
                    'active' => $user->getActiveLeadsCountAttribute(),
                    'reviewing' => $user->getReviewingLeadsCountAttribute(),
                ], 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVendorReport () {
        try {
            $user = auth()->user();

            if (!empty($user)) {
                $user = User::where('id', $user->id)->first();
                $data = $user->merchantLeads()->orderBy('created_at', 'DESC')->get();

                return response()->json($data, 200);
            } else throw new \Exception(("You are not allowed to perform this operation."));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
