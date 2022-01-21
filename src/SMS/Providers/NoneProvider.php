<?php

namespace Farzai\PhoneVerification\SMS\Providers;

use Farzai\PhoneVerification\SMS\Provider;

class NoneProvider implements Provider
{
    /**
     * @param string $phoneNumber
     * @param string $message
     */
    public function send(string $phoneNumber, string $message)
    {
        //
    }
}