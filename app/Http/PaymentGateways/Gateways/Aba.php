<?php

namespace App\Http\PaymentGateways\Gateways;

use Exception;
use App\Models\Order;
use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use App\Services\PaymentAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Aba extends PaymentAbstract
{
    protected mixed $secretKey;
    protected mixed $baseUrl;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);

        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'aba'])->first();

        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->baseUrl = 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/generate-qr'; // API URL for Sandbox environment
        }
    }

    public function payment($order, $request)
    {
        try {
            // Prepare request parameters
            $req_time = now()->format('YmdHis');
            $tran_id = $order->order_serial_no; // Use your real order number!
            $amount = number_format($order->total, 2, '.', '');
            $currency = 'USD';
            $payment_option = 'abapay_khqr';
            $lifetime = 10;
            $qr_image_template = 'template1';

            $first_name = $order->customer_first_name ?? 'Test';
            $last_name = $order->customer_last_name ?? 'User';
            $email = $order->customer_email ?? 'test@example.com';
            $phone = $order->customer_phone ?? '012345678';

            $callback_url = base64_encode(route('aba.webhook'));
            $purchase_type = 'purchase';
            $items = "";            
            $return_deeplink = "";
            $custom_fields = "";
            $return_params = "";
            $payout = "";

            $merchant_id = 'ec450110';
            $api_key = 'efe88d884970018524d4a86b13b91d83ff570c0d';

            // Concatenate string to generate hash
            $b4hash = $req_time . $merchant_id . $tran_id . $amount . $items . $first_name . $last_name . $email
                . $phone . $purchase_type . $payment_option . $callback_url . $return_deeplink . $currency
                . $custom_fields . $return_params . $payout . $lifetime . $qr_image_template;

            // Generate hash using HMAC with SHA512
            $hash = base64_encode(hash_hmac('sha512', $b4hash, $api_key, true));


            $data = [
                "req_time" => $req_time,
                "merchant_id" => $merchant_id,
                "MerchantKeyId" => $merchant_id,
                "tran_id" => $tran_id,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "email" => $email,
                "phone" => $phone,
                "amount" => $amount,
                "purchase_type" => $purchase_type,
                "payment_option" => $payment_option,
                "items" => $items,
                "currency" => $currency,
                "callback_url" => $callback_url,
                "return_deeplink" => $return_deeplink,
                "custom_fields" => $custom_fields,
                "return_params" => $return_params,
                "payout" => $payout,
                "lifetime" => $lifetime,
                "qr_image_template" => $qr_image_template,
                "hash" => $hash,
            ];
            // dd($data);

            // Make the POST request to ABA API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, $data);
            

            // Check the response and handle accordingly
            if ($response->successful()) {
                // dd($response->json());
                $responseData = $response->json();
                return view('aba_qr', compact('responseData', 'order'));
            } else {
                Log::error('ABA API Response Error: ' . $response->body());
                return redirect()->back()->with('error', 'Failed to initiate ABA payment: ' . $response->body());
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            // Log::error('ABA Payment Error: ' . $e->getMessage());
            // return redirect()->route('payment.index', [
            //     'order' => $order,
            //     'paymentGateway' => 'aba'
            // ])->with('error', 'Payment error. Please try again.');
        }
    }

    public function webhook(Request $request)
    {
        try {
            // Capture incoming request data
            $tran_id = $request->input('tran_id');
            $status = $request->input('status');
            $amount = $request->input('amount');

            // Find the order using the transaction ID
            $order = Order::where('order_serial_no', $tran_id)->first();

            if (!$order) {
                Log::warning('ABA Webhook: Order not found for tran_id: ' . $tran_id);
                return response('Order not found', 404);
            }

            // Verify if the payment status is successful
            if ($status == 'success') {
                $this->paymentService->payment($order, 'aba', $tran_id);
                return response('Payment success', 200);
            } else {
                Log::warning('ABA Webhook: Payment failed for tran_id: ' . $tran_id);
                return response('Payment failed', 400);
            }
        } catch (Exception $e) {
            Log::error('ABA Webhook Error: ' . $e->getMessage());
            return response('Server error', 500);
        }
    }

    public function status(): bool
    {
        $paymentGateway = PaymentGateway::where(['slug' => 'aba', 'status' => Activity::ENABLE])->first();
        return $paymentGateway ? true : false;
    }

    public function success($order, $request)
    {
        return redirect()->route('payment.successful', ['order' => $order])->with('success', 'Payment successful');
    }

    public function fail($order, $request)
    {
        return redirect()->route('payment.fail', ['order' => $order])->with('error', 'Payment failed');
    }

    public function cancel($order, $request)
    {
        return redirect('/checkout/payment');
    }
}
