<?php

namespace App\Models;

class Email
{
    function getResellerEmailFrom()
    {
        return 'contractor@example.com';
    }

    function getEmailsByPermit($resellerId, $event)
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}
