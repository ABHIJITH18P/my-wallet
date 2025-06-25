<?php

namespace App\Services;
use Illuminate\Support\Facades\Hash;
use Exception;


class PinValidationService
{
    
    public function validate($requestedPin, $userPin)
    {
        if(!$userPin)
            throw new Exception('Please set a PIN first');

        if(!Hash::check($requestedPin, $userPin))
            throw new Exception('Invalid PIN');
    }
}