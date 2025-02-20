<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PathaoCourierController extends Controller
{
    private $baseUrl;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;
    
    public function __construct()
    {
        $this->baseUrl = env('PATHAO_BASE_URL', 'https://courier-api-sandbox.pathao.com');
        $this->clientId = env('PATHAO_CLIENT_ID', 'your_client_id');
        $this->clientSecret = env('PATHAO_CLIENT_SECRET', 'your_client_secret');
        $this->username = env('PATHAO_USERNAME', 'test@pathao.com');
        $this->password = env('PATHAO_PASSWORD', 'lovePathao');
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
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/stores", $request->all());

        return response()->json($response->json());
    }

    // 4️⃣ Create a New Order
    public function createOrder(Request $request)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/orders", $request->all());

        return response()->json($response->json());
    }

    // 5️⃣ Create a Bulk Order
    public function createBulkOrder(Request $request)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/orders/bulk", ['orders' => $request->orders]);

        return response()->json($response->json());
    }

    // 6️⃣ Get Order Short Info
    public function getOrderInfo(Request $request, $orderId)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/orders/{$orderId}/info");

        return response()->json($response->json());
    }

    // 7️⃣ Get List of Cities
    public function getCityList(Request $request)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/city-list");

        return response()->json($response->json());
    }

    // 8️⃣ Get Zones inside a particular city
    public function getZones(Request $request, $cityId)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/cities/{$cityId}/zone-list");

        return response()->json($response->json());
    }

    // 9️⃣ Get Areas inside a particular zone
    public function getAreas(Request $request, $zoneId)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/zones/{$zoneId}/area-list");

        return response()->json($response->json());
    }

    // 🔟 Price Calculation API
    public function calculatePrice(Request $request)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->post("{$this->baseUrl}/aladdin/api/v1/merchant/price-plan", $request->all());

        return response()->json($response->json());
    }

    // 🔟 Get Merchant Store Info
    public function getMerchantStoreInfo(Request $request)
    {
        $accessToken = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken
        ])->get("{$this->baseUrl}/aladdin/api/v1/stores");

        return response()->json($response->json());
    }
}
