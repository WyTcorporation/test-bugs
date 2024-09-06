<?php

namespace App\Services;

use App\Models\Client;
use Exception;

class ClientService
{
    public function getClient(int $clientId, int $resellerId): Client
    {
        $client = Client::getById($clientId);
        if ($client === null || $client->Seller->id !== $resellerId) {
            throw new Exception('Client not found or invalid reseller association!', 400);
        }

        return $client;
    }
}
