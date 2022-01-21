<?php

namespace Farzai\PhoneVerification\Entities;

use DateTime;

/**
 * @property string reference
 * @property string code
 * @property string phone_number
 * @property \DateTimeInterface|null expires_at
 * @property \DateTimeInterface|null verified_at
 */
class Verification extends Entity
{
    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->isVerified()) {
            return true;
        }

        if ($this->expires_at && $this->expires_at < new DateTime('now')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return ! is_null($this->verified_at);
    }
}