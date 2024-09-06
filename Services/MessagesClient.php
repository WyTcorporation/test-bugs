<?php

namespace App\Services;

class MessagesClient
{
    public static function sendMessage(
        array  $messageData,
        int    $resellerId = 0,
        int    $clientId = 0,
        string $notificationEvent = '',
        int    $notificationSubEvent = 0
    ): void
    {
        echo "Message sent: " . json_encode($messageData);
    }
}
