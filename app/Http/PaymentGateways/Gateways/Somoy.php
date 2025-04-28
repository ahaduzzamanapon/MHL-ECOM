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
use GuzzleHttp\Client;

class Somoy extends PaymentAbstract
{
    protected mixed $secretKey;
    protected mixed $baseUrl;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'somoy'])->first();
        if (!blank($this->paymentGateway)) {
            $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
            $this->secretKey            = $this->paymentGatewayOption['somoy_secret_key'];
            $this->baseUrl              = $this->paymentGatewayOption['somoy_mode'] == GatewayMode::SANDBOX ? 'http://18.143.146.116/api/' : 'http://18.143.146.116/api/';
        }
    }

    public function payment($order, $request): \Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse
    {
        try {
            $client = new Client();
            $response = $client->post($this->baseUrl . 'v1/create_payment', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-SECRET-KEY' => $this->secretKey
                ],
                'json' => [
                    'currency' => 'BDT',
                    'amount' => number_format((float)$order->total, 2, '.', ''),
                    'reference' => time(),
                    'callback_url' => 'https://developer.bka.sh/',
                    'customer_name' => $order->user?->name,
                    'customer_email' => $order->user?->email,
                    'customer_phone' => $order->user?->phone,
                    'customer_address' => $order->user?->address,
                    'note' => 'Order For Food'
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                return redirect()->away(json_decode($response->getBody()->getContents())->data->payment_url);
            } else {
                return redirect()->route('payment.index', [
                    'order'          => $order,
                    'paymentGateway' => 'somoy'
                ])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.index', [
                'order'          => $order,
                'paymentGateway' => 'somoy'
            ])->with('error', $e->getMessage());
        }
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'somoy', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }

    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            if (isset($request['status_id']) && $request['status_id'] == '1') {
                $this->paymentService->payment($order, 'somoy', $request['transaction_id']);
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order'          => $order,
                    'paymentGateway' => 'somoy'
                ])->with('error', $request['msg']);
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'somoy'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'somoy'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment');
    }

    public function webhook(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $order = Order::where('order_serial_no', $request['order_id'])->first();
            return $this->success($order, $request);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'somoy'
            ])->with('error', $e->getMessage());
        }
    }
}

