<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private string $projectId;
    private string $credentialsPath;
    private string $fcmUrl;

    public function __construct()
    {
        $credentials           = json_decode(file_get_contents(storage_path('app/firebase-credentials.json')), true);
        $this->projectId       = $credentials['project_id'];
        $this->credentialsPath = storage_path('app/firebase-credentials.json');
        $this->fcmUrl          = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    // توليد Access Token
    private function getAccessToken(): string
    {
        $credentials = json_decode(file_get_contents($this->credentialsPath), true);

        $now = time();
        $payload = [
            'iss'   => $credentials['client_email'],
            'sub'   => $credentials['client_email'],
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $jwt = JWT::encode($payload, $credentials['private_key'], 'RS256');

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json('access_token');
    }

    // إرسال إشعار لمستخدم واحد
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (empty($fcmToken)) {
            return false;
        }

        try {
            $accessToken = $this->getAccessToken();

            // تحويل data values إلى strings
            $stringData = array_map('strval', $data);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post($this->fcmUrl, [
                'message' => [
                    'token'        => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data'         => $stringData,
                    'android'      => [
                        'notification' => [
                            'sound' => 'default',
                        ],
                    ],
                    'apns'         => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                            ],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::error('FCM Error: ' . $response->body());
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return false;
        }
    }

    // إرسال إشعار لعدة مستخدمين
    public function sendMulticastNotification(array $fcmTokens, string $title, string $body, array $data = []): bool
    {
        $fcmTokens = array_filter($fcmTokens);

        if (empty($fcmTokens)) {
            return false;
        }

        $success = true;
        foreach ($fcmTokens as $token) {
            if (!$this->sendNotification($token, $title, $body, $data)) {
                $success = false;
            }
        }

        return $success;
    }
}
