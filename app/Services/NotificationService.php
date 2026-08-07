<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Notify a user (saves in DB + dispatches FCM push notification).
     */
    public function notify(
        User|int $user,
        string $type,
        string $titleAr,
        string $titleEn,
        string $bodyAr,
        string $bodyEn,
        ?string $entityType = null,
        ?int $entityId = null,
        array $data = []
    ): ?Notification {
        try {
            $userId = $user instanceof User ? $user->id : $user;
            $userModel = $user instanceof User ? $user : User::find($user);

            if (!$userModel) {
                return null;
            }

            // 1. Create & save DB notification record
            $notification = Notification::create([
                'user_id'     => $userId,
                'type'        => $type,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'title_ar'    => $titleAr,
                'title_en'    => $titleEn,
                'body_ar'     => $bodyAr,
                'body_en'     => $bodyEn,
                'data'        => $data,
            ]);

            // 2. Dispatch FCM Push Notification using recipient's preferred locale
            if (!empty($userModel->fcm_token)) {
                $fcmData = array_merge([
                    'notification_id' => (string) $notification->id,
                    'type'            => $type,
                    'entity_type'     => (string) ($entityType ?? ''),
                    'entity_id'       => (string) ($entityId ?? ''),
                ], array_map('strval', $data));

                $isArabic  = ($userModel->locale === 'ar');
                $pushTitle = $isArabic ? $titleAr : $titleEn;
                $pushBody  = $isArabic ? $bodyAr  : $bodyEn;

                $this->firebaseService->sendNotification(
                    $userModel->fcm_token,
                    $pushTitle,
                    $pushBody,
                    $fcmData
                );
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('NotificationService Error: ' . $e->getMessage());
            return null;
        }
    }
}
