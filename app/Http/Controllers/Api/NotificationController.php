<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get paginated notifications for authenticated user, localized by Accept-Language header.
     */
    public function index(Request $request)
    {
        $locale = strtolower($request->header('Accept-Language', 'en'));
        $isArabic = str_contains($locale, 'ar');

        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        $notifications->getCollection()->transform(function ($item) use ($isArabic) {
            return [
                'id'          => $item->id,
                'type'        => $item->type,
                'entity_type' => $item->entity_type,
                'entity_id'   => $item->entity_id,
                'title'       => $isArabic ? $item->title_ar : $item->title_en,
                'body'        => $isArabic ? $item->body_ar : $item->body_en,
                'data'        => $item->data,
                'created_at'  => $item->created_at?->toIso8601String(),
            ];
        });

        return response()->json($notifications);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)->find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted successfully']);
    }
}
