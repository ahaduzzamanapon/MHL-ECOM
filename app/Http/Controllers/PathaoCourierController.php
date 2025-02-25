<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PathaoCourier;
use App\Models\Order;

class PathaoCourierController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    private $access_token;
    
    public function __construct()
    {
        $this->baseUrl = env('PATHAO_BASE_URL', 'https://courier-api-sandbox.pathao.com');
        $this->clientId = env('PATHAO_CLIENT_ID', 'your_client_id');
        $this->clientSecret = env('PATHAO_CLIENT_SECRET', 'your_client_secret');
        $this->username = env('PATHAO_USERNAME', 'test@pathao.com');
        $this->password = env('PATHAO_PASSWORD', 'lovePathao');
        $this->access_token = '';
    }

    // 1️⃣ Issue an Access Token
    public function issueAccessToken()
    {
        $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password
        ]);
        $this->access_token = $response->json()['access_token'];
        return response()->json($response->json());
    }

    // 2️⃣ Issue a Refresh Token
    public function issueRefreshToken(Request $request)
    {
        $refreshToken = $request->refresh_token;

        $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken
        ]);

        return response()->json($response->json());
    }

    // 3️⃣ Create a New Store
    public function createStore(Request $request)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/stores", $request->all());
        return response()->json($response->json());
    }

    // 4️⃣ Create a New Order
    public function createOrder(Request $request)
    {
        // dd($request->all());
        // {
        //     "store_id": {{merchant_store_id}},
        //     "merchant_order_id": "{{merchant_order_id}}",
        //     "recipient_name": "Demo Recipient",
        //     "recipient_phone": "017XXXXXXXX",
        //     "recipient_address": "Uttara , Sector -24, Dhaka",
        //     "recipient_city": {{city_id}},
        //     "recipient_zone": {{zone_id}},
        //     "recipient_area": {{area_id}},
        //     "delivery_type": 48,
        //     "item_type": 2,
        //     "special_instruction": "Need to Delivery before 5 PM",
        //     "item_quantity": 1,
        //     "item_weight": "0.5",
        //     "item_description": "this is a Cloth item, price- 3000",
        //     "amount_to_collect": 900
        //   }
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/orders", $request->all());

        $ress_data=$response->json();
        // array:4 [ // app\Http\Controllers\PathaoCourierController.php:94
        //     "message" => "Order Created Successfully"
        //     "type" => "success"
        //     "code" => 200
        //     "data" => array:4 [
        //       "consignment_id" => "DT230225PF6HA5"
        //       "merchant_order_id" => "11022516"
        //       "order_status" => "Pending"
        //       "delivery_fee" => 360
        //     ]
        //   ]

       

        if ($response->json()['type'] == 'success') {
             $pathao= new PathaoCourier;
        $pathao->store_id=$request->store_id;
        $pathao->merchant_order_id=$request->merchant_order_id;
        $pathao->recipient_name=$request->recipient_name;
        $pathao->recipient_phone=$request->recipient_phone;
        $pathao->recipient_address=$request->recipient_address;
        $pathao->recipient_city=$request->recipient_city;
        $pathao->recipient_zone=$request->recipient_zone;
        $pathao->recipient_area=$request->recipient_area;
        $pathao->delivery_type=$request->delivery_type;
        $pathao->item_type=$request->item_type;
        $pathao->special_instruction=$request->special_instruction;
        $pathao->item_quantity=$request->item_quantity;
        $pathao->item_weight=$request->item_weight;
        $pathao->item_description=$request->item_description;
        $pathao->amount_to_collect=$request->amount_to_collect;
        $pathao->consignment_id=$ress_data['data']['consignment_id'];
        $pathao->order_status=$ress_data['data']['order_status'];
        $pathao->delivery_fee=$ress_data['data']['delivery_fee'];
        $pathao->save();
            $order = Order::where('order_serial_no', $request->merchant_order_id)->first();
            $order->courier_id = $pathao->id;
            $order->courier_type = 'Pathao';
            $order->save();
            return ['status' => true, 'message' => "Order sent to courier Successfully"];
        }else{
            return ['status' => false, 'message' => "Order sent to courier Failed"];
        }
    }

    // 5️⃣ Create a Bulk Order
    public function createBulkOrder(Request $request)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/orders/bulk", ['orders' => $request->orders]);

        return response()->json($response->json());
    }

    // 6️⃣ Get Order Short Info
    public function getOrderInfo(Request $request, $orderId)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/orders/{$orderId}/info");

        return response()->json($response->json());
    }

    // 7️⃣ Get List of Cities
    public function getCityList(Request $request)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        //dd($accessToken);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/city-list");
        
        return response()->json($response->json());
    }

    // 8️⃣ Get Zones inside a particular city
    public function getZones(Request $request, $cityId)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/cities/{$cityId}/zone-list");

        return response()->json($response->json());
    }

    // 9️⃣ Get Areas inside a particular zone
    public function getAreas(Request $request, $zoneId)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/zones/{$zoneId}/area-list");

        return response()->json($response->json());
    }

    // 🔟 Price Calculation API
    public function calculatePrice(Request $request)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/merchant/price-plan", $request->all());

        return response()->json($response->json());
    }

    // 🔟 Get Merchant Store Info
    public function getMerchantStoreInfo(Request $request)
    {
        $this->issueAccessToken();
        $accessToken = $this->access_token;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/stores");
        return response()->json($response->json());
    }
}
