<?php

namespace Farzai\Tests\Internal;

use DateTime;
use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\Adapters\UrlAdapter;
use Farzai\PhoneVerification\Entities\Verification;

class VerifySmsUrlTestAdapter extends UrlAdapter
{
    /**
     * @var Verification
     */
    protected $entity;

    /**
     * @param Verification|null $entity
     * @return void
     */
    public function shouldUse(?Verification $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @param string $phoneNumber
     * @param string $reference
     * @param string $code
     * @return Verification|null
     */
    public function findForValidate(string $phoneNumber, string $reference, string $code): ?Verification
    {
        return $this->entity;
    }

    /**
     * @param Verification $verifier
     * @return void
     */
    public function markAsExpired(Verification $verifier): Verification
    {
        $verifier->expires_at = new DateTime('now');

        $this->entity = $verifier;

        return $verifier;
    }

    public function markAsVerified(Verification $verifier): Verification
    {
        $verifier->verified_at = new DateTime('now');

        $this->entity = $verifier;

        return $verifier;
    }

    /**
     * @param string $phoneNumber
     * @return Verification
     */
    protected function createVerification(string $phoneNumber): Verification
    {
        return new Verification(array_merge($this->entity->toArray(), [
            'phone_number' => $phoneNumber,
        ]));
    }
}