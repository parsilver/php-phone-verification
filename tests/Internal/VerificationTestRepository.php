<?php

namespace Farzai\Tests\Internal;

use DateTime;
use Farzai\PhoneVerification\Entities\Verification;
use Farzai\PhoneVerification\Repositories\VerificationRepository;

class VerificationTestRepository implements VerificationRepository
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
    public function create(string $phoneNumber): Verification
    {
        return new Verification(array_merge($this->entity->toArray(), [
            'phone_number' => $phoneNumber,
        ]));
    }
}