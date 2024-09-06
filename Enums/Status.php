<?php

namespace App\Enums;

enum Status: string
{
    case COMPLETED = 'Completed';
    case PENDING = 'Pending';
    case REJECTED = 'Rejected';

    public static function getName(int $id): string
    {
        $statusMap = [
            0 => self::COMPLETED->value,
            1 => self::PENDING->value,
            2 => self::REJECTED->value,
        ];

        return $statusMap[$id] ?? 'Unknown';
    }
}
