<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->route('bl.show', $notification->data['bill_of_lading_id']);
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('notifications.index');
    }
}
