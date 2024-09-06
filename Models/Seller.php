<?php

namespace App\Models;

class Seller extends Contractor
{
    public static function getById(int $sellerId): ?self
    {
        return new self();
    }
}
