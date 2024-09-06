<?php
namespace App\Enums;

enum NotificationType: int
{
    case TYPE_NEW = 1;
    case TYPE_CHANGE = 2;
}
