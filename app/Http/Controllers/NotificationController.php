<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class NotificationController extends Controller
{


    public function showNotifications()
    {
        $notifications = DB::table('notifications')->orderBy('created_at', 'desc')->get();
        return response()->json($notifications);
    }


    public function clear()
    {
        DB::table('notifications')->truncate(); // Deletes all notifications

        return response()->json(['message' => 'All notifications cleared successfully!'], 200);
    }


    public function getNotifications()
    {
        $notifications = DB::table('notifications')->latest()->take(10)->get(); // Fetch latest 10
        $count = $notifications->count(); // Count the retrieved notifications

        return response()->json([
            'count' => $count,
            'notifications' => $notifications
        ]);
    }
}
