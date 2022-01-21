<?php

namespace Farzai\PhoneVerification\SMS;

interface Provider
{
    /**
     * @param string $phoneNumber
     * @param string $message
     * @param array $options
     * @return mixed
     */
    public function send(string $phoneNumber, string $message, array $options = []);
}