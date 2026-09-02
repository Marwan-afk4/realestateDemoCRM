<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Models\PushNotification;
use App\Models\User;
use App\Services\FirebaseCloudMessaging;
use Illuminate\Http\Request;
use RuntimeException;

class PushNotificationController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'DESC');

        $notifications = PushNotification::with('sender')
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        $deviceCount = DeviceToken::count();

        return view('push-notifications.index', compact('notifications', 'sortField', 'sortOrder', 'deviceCount'));
    }

    public function create()
    {
        $users = User::query()
            ->where('role', '!=', 'admin')
            ->whereHas('deviceTokens')
            ->withCount('deviceTokens')
            ->orderBy('first_name')
            ->get();

        $deviceCount = DeviceToken::whereHas('user', fn ($query) => $query->where('role', '!=', 'admin'))->count();

        return view('push-notifications.create', compact('users', 'deviceCount'));
    }

    public function store(Request $request, FirebaseCloudMessaging $fcm)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'audience' => 'required|in:all,selected',
            'user_ids' => 'required_if:audience,selected|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $tokensQuery = DeviceToken::query()->whereHas('user', function ($query) use ($validated) {
            $query->where('role', '!=', 'admin');

            if ($validated['audience'] === 'selected') {
                $query->whereIn('id', $validated['user_ids'] ?? []);
            }
        });

        $tokens = $tokensQuery->pluck('token')->all();

        if ($tokens === []) {
            return back()
                ->withInput()
                ->with('error', __('No registered mobile devices found for the selected audience.'));
        }

        set_time_limit(120);

        try {
            $result = $fcm->sendToTokens($tokens, $validated['title'], $validated['body']);
        } catch (RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        PushNotification::create([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'audience' => $validated['audience'],
            'user_ids' => $validated['audience'] === 'selected' ? array_values($validated['user_ids']) : null,
            'sent_count' => $result['sent'],
            'failed_count' => $result['failed'],
            'sent_by' => $request->user()->id,
        ]);

        $message = __('Notification sent to :sent device(s).', ['sent' => $result['sent']]);

        if ($result['failed'] > 0) {
            $message .= ' '.__(':failed failed.', ['failed' => $result['failed']]);
        }

        return redirect()
            ->route('push-notifications.index')
            ->with($result['sent'] > 0 ? 'success' : 'error', $message);
    }

    public function show(PushNotification $push_notification)
    {
        $push_notification->load('sender');

        $recipients = collect();
        if ($push_notification->audience === 'selected' && $push_notification->user_ids) {
            $recipients = User::whereIn('id', $push_notification->user_ids)->get();
        }

        return view('push-notifications.show', [
            'notification' => $push_notification,
            'recipients' => $recipients,
        ]);
    }

    public function destroy(PushNotification $push_notification)
    {
        $push_notification->delete();

        return redirect()
            ->route('push-notifications.index')
            ->with('success', __('Notification record deleted.'));
    }
}
