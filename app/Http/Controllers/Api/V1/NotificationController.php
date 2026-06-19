<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Http\Resources\Api\V1\NotificationResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest('sent_at')->get();

        // Auto-seed mock notifications if the user doesn't have any, for immediate manual testing
        if ($notifications->isEmpty()) {
            $now = Carbon::now();
            $mockData = [
                [
                    'title' => 'Selamat datang di Trackerin! 🚀',
                    'message' => 'Mulai susun rencana belajarmu sekarang dan capai impianmu dengan panduan kurikulum AI!',
                    'is_read' => false,
                    'sent_at' => $now->copy()->subHours(2),
                ],
                [
                    'title' => 'AI Learning Assistant Siap ✨',
                    'message' => 'Anda dapat membuat roadmap belajar kustom pada menu Explore dengan bantuan AI.',
                    'is_read' => false,
                    'sent_at' => $now->copy()->subHours(12),
                ],
                [
                    'title' => 'Tips Belajar Hari Ini 💡',
                    'message' => 'Konsistensi adalah kunci. Luangkan waktu minimal 30 menit setiap hari untuk belajar!',
                    'is_read' => true,
                    'sent_at' => $now->copy()->subDays(1),
                ],
            ];

            foreach ($mockData as $data) {
                $user->notifications()->create($data);
            }

            // Fetch again after seeding
            $notifications = $user->notifications()->latest('sent_at')->get();
        }

        return NotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, Notification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return new NotificationResource($notification);
    }
}
