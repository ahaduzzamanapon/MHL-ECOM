<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransectionChack;
use App\Models\Order;
use App\Models\User;

class TransectionCheckController extends Controller
{
    public function index()
    {
        $TransectionChack = TransectionChack::where('if_old', 'no')->get();
        foreach ($TransectionChack as $key => $value) {
            $success_url = $value->success_url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $success_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        $time_ago = \Carbon\Carbon::now()->subMinutes(10)->toDateTimeString();
        TransectionChack::where('created_at', '<', $time_ago)->delete();
        return true;
    }
    public function get_aba_config_data(Request $request)
    {
        $order_id = $request->order_id;
        $methud = $request->methud;

        $order = Order::find($order_id);
        $user = User::find($order->user_id);

        $success_url = route('payment.success', [
            'order' => $order,
            'paymentGateway' => $methud
        ]);
        $fail_url = route('payment.fail', [
            'order' => $order,
            'paymentGateway' => $methud
        ]);
        $return_url = route('payment.return', [
            'order' => $order,
            'paymentGateway' => $methud
        ]);

        // Generate data
        $transactionId = 'trn-' . time() . str_pad((string) rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $amount = $order->total;
        $firstName = $user->name;
        $lastName = $user->name;
        $phone = $user->phone ?? '012345678'; // fallback if null
        $email = $user->email ?? 'demo@example.com';
        $req_time = time();
        $merchant_id = 'adoralifestyle';
        if ($methud == 'card') {
            $payment_option = 'cards';
        } else {
            $payment_option = 'abapay_khqr';
        }
        $currency = 'usd';
        // Save transaction log (optional)
        $check = new TransectionChack();
        $check->transaction_id = $transactionId;
        $check->order = $order->id;
        $check->success_url = $success_url;
        $check->save();

        // Hash generation logic
        $hash_str = $req_time . $merchant_id . $transactionId . $amount . $firstName . $lastName . $email . $phone . $payment_option . $return_url . $fail_url . $success_url . $currency;
        $api_key = 'c3dabc49-77db-4b9a-b277-c8ad3d256fba'; // ABA API Key
        $hash = base64_encode(hash_hmac('sha512', $hash_str, $api_key, true));

        return response()->json([
            'success' => true,
            'form_data' => [
                'hash' => $hash,
                'tran_id' => $transactionId,
                'amount' => $amount,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'req_time' => $req_time,
                'merchant_id' => $merchant_id,
                'payment_option' => $payment_option,
                'return_url' => $return_url,
                'cancel_url' => $fail_url,
                'continue_success_url' => $success_url,
                'currency' => $currency,
            ]
        ]);
    }
}

