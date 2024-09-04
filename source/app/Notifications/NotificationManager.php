<?php

namespace App\Notifications;

class NotificationManager
{
    public static function send(
        $resellerId,
        $clientid,
        $event,
        $notificationSubEvent,
        $templateData,
        &$errorText,
        $locale = null
    )
    {
        // fakes the method
        return true;
    }
}
