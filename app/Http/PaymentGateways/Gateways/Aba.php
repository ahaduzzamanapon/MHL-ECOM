<?php

namespace App\Http\PaymentGateways\Gateways;


use App\Enums\Activity;
use App\Enums\GatewayMode;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Services\PaymentAbstract;
use Exception;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;

class Aba extends PaymentAbstract
{
    public mixed $response;

    public function __construct()
    {
        $paymentService = new PaymentService();
        parent::__construct($paymentService);
        $this->paymentGateway       = PaymentGateway::with('gatewayOptions')->where(['slug' => 'aba'])->first();
        $this->paymentGatewayOption = $this->paymentGateway->gatewayOptions->pluck('value', 'option');
    }

    public function payment($order, $request)
    {
            $array= [];
            $array['success_url']      = route('payment.success', [
                'order'          => $order,
                'paymentGateway' => 'aba'
            ]);
            $array['fail_url']         = route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'aba'
            ]);
            $array['cancel_url']       = route('payment.cancel', [
                'order'          => $order,
                'paymentGateway' => 'aba'
            ]);

         return view('aba_qr', compact('order', 'array'));
    }

    public function status(): bool
    {
        $paymentGateways = PaymentGateway::where(['slug' => 'aba', 'status' => Activity::ENABLE])->first();
        if ($paymentGateways) {
            return true;
        }
        return false;
    }



    public function success($order, $request): \Illuminate\Http\RedirectResponse
    {
        
        try {
                $paymentService = new PaymentService;
                $paymentService->payment($order, 'aba', 'aba');
                return redirect()->route('payment.successful', ['order' => $order])->with('success', trans('all.message.payment_successful'));
           
        } catch (Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return redirect()->route('payment.fail', [
                'order'          => $order,
                'paymentGateway' => 'aba'
            ])->with('error', $e->getMessage());
        }
    }

    public function fail($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('payment.index', ['order' => $order,'paymentGateway' => 'aba'])->with('error', trans('all.message.something_wrong'));
    }

    public function cancel($order, $request): \Illuminate\Http\RedirectResponse
    {
        return redirect('/checkout/payment');
    }
}
