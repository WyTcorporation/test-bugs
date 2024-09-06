<?php

namespace App\Services;

use App\Models\Seller;
use Exception;

class SellerService
{
    public function getSeller(int $sellerId): Seller
    {
        $seller = Seller::getById($sellerId);
        if ($seller === null) {
            throw new Exception('Seller not found!', 400);
        }

        return $seller;
    }
}
