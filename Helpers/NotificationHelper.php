<?php

namespace App\Helpers;



class NotificationHelper
{
    public static function getResellerEmailFrom(int $resellerId): string
    {
        return 'reseller@example.com';
    }

    public static function getEmailsByPermit(int $resellerId, string $event): array
    {
        return [
            'employee1@example.com',
            'employee2@example.com'
        ];
    }
}
