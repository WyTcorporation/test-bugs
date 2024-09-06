<?php

namespace App\Helpers;

use App\Enums\Emails;

class NotificationHelper
{
    public static function getResellerEmailFrom(int $resellerId): Emails
    {
        return Emails::RESELLER;
    }

    public static function getEmailsByPermit(int $resellerId, string $event): array
    {
        return [
            Emails::EMPLOYEE1,
            Emails::EMPLOYEE2
        ];
    }
}
