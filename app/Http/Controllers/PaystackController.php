<?php

namespace App\Http\Controllers;

use App\Services\PaystackService;
use App\Services\SettingService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaystackController extends Controller
{
    private PaystackService $paystackService;
    private SettingService $settingService;

    public function __construct(PaystackService $paystackService, SettingService $settingService)
    {
        $this->paystackService = $paystackService;
        $this->settingService = $settingService;
    }

    /**
     * Handle Paystack payment callback.
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');
        $trxref = $request->query('trxref');
        
        if (!$reference && !$trxref) {
            return redirect()->route('credits.purchase')
                ->with('error', 'Invalid payment reference.');
        }

        $reference = $reference ?: $trxref;

        try {
            // Verify payment with Paystack
            $verification = $this->paystackService->verifyPayment($reference);
            
            if (!$verification['success']) {
                return redirect()->route('credits.purchase')
                    ->with('error', 'Payment verification failed: ' . $verification['message']);
            }

            $paymentData = $verification['data'];
            
            // Find the transaction
            $transaction = Transaction::where('reference', $reference)
                ->orWhere('metadata->paystack_reference', $reference)
                ->first();

            if (!$transaction) {
                Log::warning('Transaction not found for payment callback', [
                    'reference' => $reference,
                    'paystack_data' => $paymentData,
                ]);
                
                return redirect()->route('credits.purchase')
                    ->with('error', 'Transaction not found.');
            }

            // Check if user owns this transaction
            if (Auth::check() && $transaction->user_id !== Auth::id()) {
                Log::warning('User attempted to access transaction they do not own', [
                    'user_id' => Auth::id(),
                    'transaction_user_id' => $transaction->user_id,
                    'transaction_id' => $transaction->id,
                ]);
                
                return redirect()->route('credits.purchase')
                    ->with('error', 'Unauthorized access to transaction.');
            }

            // Handle payment status
            if ($paymentData['status'] === 'success') {
                if ($transaction->isConfirmed()) {
                    return redirect()->route('dashboard')
                        ->with('success', 'Payment already confirmed! Credits have been added to your account.');
                }

                // Process successful payment via webhook handler
                $webhookResult = $this->paystackService->handleWebhook([
                    'event' => 'charge.success',
                    'data' => $paymentData,
                ], 'callback_verification');

                if ($webhookResult['success']) {
                    return redirect()->route('dashboard')
                        ->with('success', 'Payment successful! ' . number_format($transaction->amount) . ' credits have been added to your account.');
                } else {
                    Log::error('Failed to process successful payment in callback', [
                        'transaction_id' => $transaction->id,
                        'webhook_result' => $webhookResult,
                    ]);
                    
                    return redirect()->route('credits.purchase')
                        ->with('error', 'Payment was successful but failed to process. Please contact support.');
                }
            } else {
                // Handle failed payment
                $failureReason = $paymentData['gateway_response'] ?? 'Payment failed';
                
                $webhookResult = $this->paystackService->handleWebhook([
                    'event' => 'charge.failed',
                    'data' => $paymentData,
                ], 'callback_verification');

                return redirect()->route('credits.purchase')
                    ->with('error', 'Payment failed: ' . $failureReason . '. You can retry the payment from your transaction history.');
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('credits.purchase')
                ->with('error', 'An error occurred while processing your payment. Please contact support if the issue persists.');
        }
    }

    /**
     * Handle Paystack webhooks.
     */
    public function webhook(Request $request)
    {
        try {
            // Get the payload and signature
            $payload = $request->all();
            $signature = $request->header('X-Paystack-Signature');

            if (!$signature) {
                Log::warning('Paystack webhook received without signature', [
                    'payload' => $payload,
                    'headers' => $request->headers->all(),
                ]);
                
                return response()->json(['error' => 'Missing signature'], 400);
            }

            // Process the webhook
            $result = $this->paystackService->handleWebhook($payload, $signature);

            if ($result['success']) {
                Log::info('Paystack webhook processed successfully', [
                    'event' => $payload['event'] ?? 'unknown',
                    'reference' => $payload['data']['reference'] ?? null,
                ]);
                
                return response()->json(['message' => 'Webhook processed successfully']);
            } else {
                Log::warning('Paystack webhook processing failed', [
                    'event' => $payload['event'] ?? 'unknown',
                    'reference' => $payload['data']['reference'] ?? null,
                    'error' => $result['message'],
                ]);
                
                return response()->json(['error' => $result['message']], 400);
            }

        } catch (\Exception $e) {
            Log::error('Paystack webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Get Paystack public key for frontend.
     */
    public function getPublicKey()
    {
        if (!$this->paystackService->isEnabled()) {
            return response()->json([
                'error' => 'Paystack payment service is currently disabled.',
            ], 503);
        }

        return response()->json([
            'public_key' => $this->paystackService->getPublicKey(),
            'pricing' => $this->paystackService->getPricingConfig(),
        ]);
    }

    /**
     * Initialize payment (alternative to Livewire component).
     */
    public function initializePayment(Request $request)
    {
        if (!$this->paystackService->isEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Paystack payment service is currently disabled.',
            ], 503);
        }

        $minimumAmount = $this->settingService->get('minimum_amount_naira', 300.0);
        $minimumCredits = $this->settingService->get('minimum_credits_purchase', 100);
        
        $request->validate([
            'amount' => "required|numeric|min:{$minimumAmount}",
            'credits' => "required|integer|min:{$minimumCredits}",
        ]);

        try {
            $user = Auth::user();
            
            $result = $this->paystackService->initializePayment(
                $user->id,
                $request->amount,
                $user->email,
                route('paystack.callback'),
                [
                    'source' => 'api',
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                ]
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'authorization_url' => $result['authorization_url'],
                        'access_code' => $result['access_code'],
                        'reference' => $result['reference'],
                        'transaction_id' => $result['transaction_id'],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment initialization API error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment status (for AJAX calls).
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        try {
            $result = $this->paystackService->verifyPayment($request->reference);
            
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Payment verification API error', [
                'reference' => $request->reference,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}