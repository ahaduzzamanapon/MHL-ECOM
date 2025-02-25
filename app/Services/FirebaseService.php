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
