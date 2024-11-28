<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function sendNotification($receiverId, $message)
    {
        // Create a new notification entry
        Notification::create([
            'receiver_id' => $receiverId,
            'message' => $message,
        ]);

        // You can implement the actual notification mechanism here
        // For example: sending an email, SMS, or push notification
    }

    public static function getMessagesByUserId($userId)
    {
        // Retrieve messages for the given user ID
        return Notification::where('receiver_id', $userId)->get();
    }
}