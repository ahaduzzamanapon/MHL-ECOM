<?php

namespace App\Services;


use Exception;
use GuzzleHttp\Client;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    public $filePath;

    public function sendNotification($data, $fcmTokens, $topicName)
    {
        try {
            $notification = Settings::group('notification')->all();
            $url = 'https://fcm.googleapis.com/v1/projects/' . $notification['notification_fcm_project_id'] . '/messages:send';
            $accessToken = $this->getAccessToken();
            $client  = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];
            foreach ($fcmTokens as $fcmToken) {
                $payload = [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $data->title,
                            'body' => $data->description,
                            'image' => $data->image ?? null,
                        ],
                        'data' => [
                            'title' => $data->title,
                            'body' => $data->description,
                            'sound' => 'default',
                            'image' => $data->image ?? null,
                            'topicName' => $topicName,
                        ],
                        'webpush' => [
                            "headers" => [
                                "Urgency" => "high"
                            ]
                        ],
                    ],
                ];


                $result = $client->post($url, [
                    'headers' => $headers,
                    "body"    => json_encode($payload)
                ]);
                return $result->getBody()->getContents();
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            Log::info($e->getMessage());
            return $e->getMessage();
        }
    }
    public function sendNotification_one($data, $fcmToken, $topicName)
    {
        try {
            // Check if FCM Token is provided
            if (empty($fcmToken)) {
                throw new Exception("FCM Token is required");
            }
            // Retrieve notification settings
            $notification = Settings::group('notification')->all();
            $url = 'https://fcm.googleapis.com/v1/projects/unicom-c298a/messages:send';
            $accessToken ="ya29.c.c0ASRK0GaKd0-8KXBIFw7Gj2wpaIioDeSOD5OMi1C0o_fuhfYVbl_A-PjE6D8y9fENXeEgwA7C9cx7tAtGbbra951PqTID24FOggMNyX45YiAqyhcraJL--wjUhyt65gykSHGjhPbEVy3suwXw67HOC-COHyVmkTN_S08sm4VkshZ61IgUVF9HvsE9RxkSaDqG4g-qkXGIFRgdzITWLi2h9YcwnucYAalnhZBEZW7TeeeDSrDt3OKNNAJVb3vkB3hf5c09PkKkuv6HjGT95HHzXptTriVOGNyrvLfuB45rAAUTQ7D99V7AH85QuGkMw8pES91EFCjMt0p0B3U45WATUjMT6iKXNqgweWYH7gnqmWeyYQVBEKD0EVLRE385PJvo1r7vQa9bWRrbQcSpfUl18ZRiXdVzBQayUqFe1-uaJR9mFSeXa2311V1n97lefnjbObmxndt7_qxWrVrpgB6zI8MYkk5arR-qbt3pp_zB7jn6kx26nsr5wVd8I21U7R9ij4tiF39dh7V6_sujvx7nYruWcFb6gxc7WXqY9Ruca3rW1hXb68BZh-XW7MVW8X4f6FoOg9eyn1byw4FXIpOYauRVRv6s6cvegtsd2jOkbdXaf0Bvuint20d118hJ3S008YazeVi0ee5i1cpMe9tYyWV3icff9VrFetU9oI5XzQuiXiixXwjcfxofevqrBh2MFcBm5777oFW09BmO9uZo8ozZlasReYymlqsFY2iRZBIb5eVyckmWoQMM6YmxUm9Zqro_XbsSbVfuOJpuYnQVJO3l3Osng6XynsojemvyhxapvJQ_sSBb2Zy94QpS-ejqiyb1QVuigMju7Ba15rI-U0-3Vza7fBq9uha3JI7vw4voY8VtuRWl4Wggzkv-8pRBxQS2agJfB1Jju4kvqwitm0pUph186SjoMy55-m3nxSy4dO6ZyzBzb3rQc8SemQvgYnqRYFaXaot83hXSRnxrMs00fWIOlW4s5iVk9e7u0R6izJ-B1pZkMho";
            // Check if access token is valid
            if (empty($accessToken)) {
                throw new Exception("Access Token is invalid");
            }
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];

            // Prepare notification payload
            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $data->title,
                        'body'  => $data->description,
                        'image' => isset($data->image) ? $data->image : null,
                    ],
                    'data' => [
                        'title' => $data->title,
                        'body'  => $data->description,
                        'sound' => 'default',
                        'image' => isset($data->image) ? $data->image : null,
                        'topicName' => $topicName,
                    ],
                    'webpush' => [
                        'headers' => [
                            'Urgency' => 'high',
                        ],
                    ],
                ],
            ];
            // Send POST request to FCM
            $result = $client->post($url, [
                'headers' => $headers,
                'body'    => json_encode($payload),
            ]);

            return $result->getBody()->getContents();
        } catch (Exception $e) {
            //dd($e->getMessage());
            Log::error("FCM Notification Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function sendNotification_multi($data, $fcmTokens, $topicName)
    {
        try {
            // Check if FCM Tokens are provided
            if (empty($fcmTokens)) {
                throw new Exception("FCM Tokens are required");
            }
    
            // Retrieve notification settings
            $notification = Settings::group('notification')->all();
            $url = 'https://fcm.googleapis.com/v1/projects/' . $notification['notification_fcm_project_id'] . '/messages:send';
            $accessToken = $this->getAccessToken();
            
            // Check if access token is valid
            if (empty($accessToken)) {
                throw new Exception("Access Token is invalid");
            }
    
            $client = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];
    
            // Prepare notification payload for multiple tokens
            $payload = [
                'messages' => array_map(function ($token) use ($data, $topicName) {
                    return [
                        'token' => $token,
                        'notification' => [
                            'title' => $data->title,
                            'body'  => $data->description,
                            'image' => isset($data->image) ? $data->image : null,
                        ],
                        'data' => [
                            'title' => $data->title,
                            'body'  => $data->description,
                            'sound' => 'default',
                            'image' => isset($data->image) ? $data->image : null,
                            'topicName' => $topicName,
                        ],
                        'webpush' => [
                            'headers' => [
                                'Urgency' => 'high',
                            ],
                        ],
                    ];
                }, $fcmTokens),
            ];
    
            // Send POST request to FCM
            $result = $client->post($url, [
                'headers' => $headers,
                'body'    => json_encode($payload),
            ]);
    
            return $result->getBody()->getContents();
        } catch (Exception $e) {
            dd($e->getMessage());
            Log::error("FCM Notification Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    


    function getAccessToken()
    {

        $keyFilePath = NotificationSetting::where(['key' => 'notification_fcm_json_file'])->first()->file;
        $parsed_url = parse_url($keyFilePath);

        if (isset($parsed_url['path'])) {
            $relative_path = ltrim($parsed_url['path'], '/storage');
            $this->filePath = storage_path('app/public/' . $relative_path);
        } else {
            throw new Exception('No file found in the URL');
        }

        $SCOPES = ['https://www.googleapis.com/auth/cloud-platform'];

        if (!file_exists($this->filePath)) {
            throw new Exception('Service account key file not found');
        }

        $credentials = new ServiceAccountCredentials($SCOPES, $this->filePath);
        $token = $credentials->fetchAuthToken();

        if (isset($token['access_token'])) {
            return $token['access_token'];
        } else {
            throw new Exception('Failed to fetch access token');
        }
    }
}
