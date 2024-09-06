<?php

namespace App\Models;

class Client
{
    public int $id;
    public string $name;
    public string $email;
    public string $mobile;
    public int $type;
    public Seller $Seller;

    public static function getById(int $clientId): ?self
    {
        // Mock data fetching logic
        return new self();
    }

    public function getFullName(): string
    {
        return $this->name;
    }
}
