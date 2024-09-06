<?php


namespace App\Services;

class NotificationManager
{
    public static function send(
        int    $resellerId,
        int    $clientId,
        string $event,
        int    $notificationSubEvent,
        array  $templateData,
        string &$errorText
    ): bool
    {
        $errorText = '';
        return true;
    }
}
