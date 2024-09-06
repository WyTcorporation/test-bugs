<?php

namespace App\Models;

class Contractor
{
    public int $id;
    public int $type;
    public string $name;

    public static function getById(int $id): ?self
    {
        return new self();
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}
