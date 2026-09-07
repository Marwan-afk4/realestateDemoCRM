<?php

namespace App\Http\Controllers\Api\Crm;

use App\Http\Controllers\Api\Crm\Concerns\RespondsJson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use RespondsJson;

    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->limit(50)->get();

        return $this->ok([
            'unread_count' => $request->user()->unreadNotifications()->count(),
            'notifications' => $notifications->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => class_basename($notification->type),
                'data' => $notification->data,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->toIso8601String(),
            ]),
        ]);
    }

    public function markRead(Request $request, string $notification)
    {
        $request->user()
            ->notifications()
            ->where('id', $notification)
            ->first()
            ?->markAsRead();

        return $this->ok(null, __('Notification marked as read.'));
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return $this->ok(null, __('All notifications marked as read.'));
    }
}
