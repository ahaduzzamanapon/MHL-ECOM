<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\Currency;
use App\Models\TransectionChack;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use Exception;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use GuzzleHttp\Client;
use Illuminate\Http\Request;


class Card extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway = PaymentGateway::with('gatewayOptions')->where(['slug' => 'card'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
    }

    public function payment($order, $request)
    {
        $array = [];
        $array['success_url'] = route('payment.success', [
            'order' => $order,
            'paymentGateway' => 'card'
        ]);
        $array['fail_url'] = route('payment.fail', [
            'order' => $order,
            'paymentGateway' => 'card'
        ]);
        $array['return_url'] = route('payment.return', [
            'order' => $order,
            'paymentGateway' => 'card'
        ]);

        return view('aba_card', compact('order', 'array'));
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'card', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }



    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $api_key = "c3dabc49-77db-4b9a-b277-c8ad3d256fba";
            $merchant_id = 'adoralifestyle';
            $req_time = time();
            
            $TransectionChack = TransectionChack::where('order', $order->id)->first();
            if (!$TransectionChack) {
                return redirect()->route('payment.fail', [
                    'order' => $order,
                    'paymentGateway' => 'card'
                ]);
            }
            $tran_id = $TransectionChack->transaction_id;
            $b4hash = $req_time . $merchant_id . $tran_id;
            $hash = base64_encode(hash_hmac('sha512', $b4hash, $api_key, true));

            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json'
            ];
            $body = [
                'req_time' => $req_time,
                'merchant_id' => $merchant_id,
                'tran_id' => $tran_id,
                'hash' => $hash
            ];
            $response = $client->post('https://checkout.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2', [
                'headers' => $headers,
                'json' => $body
            ]);

            $responseData = json_decode($response->getBody(), true);
            

            if ($responseData['data']['payment_status_code'] == 0) {
                $TransectionChack->update([
                    'if_old' => 'yes'
                ]);
                session()->forget('transaction_id');
                $paymentService = new PaymentService;
                $paymentService->payment($order, 'card', $tran_id);
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
            } else {
                return redirect()->route('payment.fail', [
                    'order' => $order,
                    'paymentGateway' => 'card'
                ])->with('error', trans('all.message.something_wrong'));
            }
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order' => $order,
                'paymentGateway' => 'card'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order, 'paymentGateway' => 'card'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment');
    }
    public function return($order, $request): \Illuminate\Http\RedirectResponse
    {
        return true;

    }
}
