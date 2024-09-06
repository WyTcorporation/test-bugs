<?php

namespace App\Models;

class Employee extends Contractor
{
    public static function getById(int $employeeId): ?self
    {
        return new self();
    }
}
