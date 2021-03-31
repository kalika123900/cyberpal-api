<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Projects;
use App\Proposals;
// use Cartalyst\Stripe\Stripe;

class PaymentsController extends Controller
{
    // - TODO - Auth Check
    public function initiateProjectPayment ($id) {
        // - Get Project
        try {
            $receipt_email = auth()->user()->email;

            $proposal = Proposals::where('id', $id)->with('project')->first();

            if (empty($proposal)) {
                return abort(500, 'Proposal not found.');
            }

            if (empty($proposal['proposed_price'])) {
                return abort(500, 'Proposal doesnot have a valid amount.');
            }

            if ($proposal['isAccepted'] === "1" || $proposal['isAccepted'] === 1) {
                    $description = "ID: #" . $proposal->project['reference_id'];

                    $stripe = new \Stripe\StripeClient(env('STRIPE_PUBLISHING_KEY', null));

                    $token = $stripe->paymentIntents->create([
                        'currency' => 'GBP',
                        // 'currency' => 'INR',
                        'amount'   => (int)$proposal['proposed_price'],
                        'description' => $description,
                        'receipt_email' => $receipt_email,
                        'statement_descriptor_suffix' => $description,
                        'payment_method_types' => ['card'],
                    ]);
                
                    return response()->json($token['client_secret'], 200);
            } else abort(500, 'You need to accept a proposal before payment');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
