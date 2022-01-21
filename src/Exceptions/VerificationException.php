<?php

namespace Farzai\PhoneVerification\Exceptions;

class VerificationException
{
    /**
     * Token expired
     *
     * @return VerificationHasExpired
     */
    public static function expired()
    {
        return new VerificationHasExpired("The verification code is expired");
    }

    /**
     * @return InvalidVerificationException
     */
    public static function invalid()
    {
        return new InvalidVerificationException("The verification code is invalid.");
    }
}