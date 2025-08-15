<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Order;
use App\Models\SteadfastCourier;
use App\Models\RedexCourierModel;
use App\Models\PathaoCourier;
use App\Exports\OrderExport;
use App\Services\OrderService;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\PaymentStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TransectionChack;


class OnlineOrderController extends AdminController
{
    private OrderService $orderService;

    public function __construct(OrderService $order)
    {
        parent::__construct();
        $this->orderService = $order;
        $this->middleware(['permission:online-orders'])->only(
            'index',
            'show',
            'export',
            'changeStatus',
            'changePaymentStatus'
        );
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderDetailsResource::collection($this->orderService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function sendCourier(Request $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return response($this->orderService->sendCourier($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function courier_status(Request $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $req=$request->all();
            $order_id=$req['invoice'];
            $status=$req['status'];
            $steadfast=SteadfastCourier::where('invoice', $order_id)->first();
            $steadfast->status=$status;
            $steadfast->save();
            return response($steadfast);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Order $order): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->show($order, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function get_aba_config_data(Request $request)
    {
        return response()->json(['success' => true, 'data' => $request->all()]);
        $order_id = $request->order_id;
        $method = $request->methud;

        $order = Order::find($order_id);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found']);
        }

        $user = User::find($order->user_id);
        $transactionId = 'trn-' . time() . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $amount = $order->total;
        $req_time = time();
        $merchant_id = 'adoralifestyle';
        $payment_option = 'abapay_khqr';

        // You can replace these with frontend-defined or env routes
        $return_url = url('/payment/return');
        $cancel_url = url('/payment/cancel');
        $continue_success_url = url('/payment/success');
        $currency = 'usd';

        // Create the hash
        $hash = base64_encode(hash_hmac('sha512', 
            $req_time . $merchant_id . $transactionId . $amount . 
            $user->name . $user->name . $user->email . $user->phone . 
            $payment_option . $return_url . $cancel_url . $continue_success_url . $currency,
            'c3dabc49-77db-4b9a-b277-c8ad3d256fba', // API key
            true
        ));

        // Save transaction log
        TransectionChack::create([
            'transaction_id' => $transactionId,
            'order' => $order_id,
            'success_url' => $continue_success_url
        ]);

        // Generate raw HTML form (as string)
        $form = '
        <form method="POST" target="aba_webservice" action="https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase" id="aba_merchant_request">
            <input type="hidden" name="hash" value="' . $hash . '" />
            <input type="hidden" name="tran_id" value="' . $transactionId . '" />
            <input type="hidden" name="amount" value="' . $amount . '" />
            <input type="hidden" name="firstname" value="' . $user->name . '" />
            <input type="hidden" name="lastname" value="' . $user->name . '" />
            <input type="hidden" name="phone" value="' . $user->phone . '" />
            <input type="hidden" name="email" value="' . $user->email . '" />
            <input type="hidden" name="req_time" value="' . $req_time . '" />
            <input type="hidden" name="merchant_id" value="' . $merchant_id . '" />
            <input type="hidden" name="payment_option" value="' . $payment_option . '" />
            <input type="hidden" name="return_url" value="' . $return_url . '" />
            <input type="hidden" name="cancel_url" value="' . $cancel_url . '" />
            <input type="hidden" name="continue_success_url" value="' . $continue_success_url . '" />
            <input type="hidden" name="currency" value="' . $currency . '" />
        </form>';

     

        try {
            return response()->json([
                'success' => true,
                'form' => $form
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 422);
        }
    }


    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new OrderExport($this->orderService, $request), 'Online-Order.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(Order $order, OrderStatusRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->changeStatus($order, $request, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changePaymentStatus(Order $order, PaymentStatusRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->orderService->changePaymentStatus($order, $request, false));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function checkCourierStatus(Request $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        $order = Order::where('id', $request->order_id)->first();
        // dd($order->courier_id);
        try {
            if ($order->courier_id != null && $order->courier_type == 'Steadfast') { 
                $info = SteadfastCourier::where('invoice', $order->order_serial_no)->first();
                return response(['status' => true, 'data' => $info,'courier_name'=> $order->courier_type], 200);
            }elseif ($order->courier_id != null && $order->courier_type == 'Redex') { 
                $info = RedexCourierModel::where('invoice', $order->order_serial_no)->first();
                return response(['status' => true, 'data' => $info,'courier_name'=> $order->courier_type], 200);
            } elseif ($order->courier_id != null && $order->courier_type == 'Pathao') { 
                $info = PathaoCourier::where('merchant_order_id', $order->order_serial_no)->first();
                return response(['status' => true, 'data' => $info,'courier_name'=> $order->courier_type], 200);
            } 
            else {
                 return new OrderDetailsResource($this->orderService->show($order, false));
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }




}
