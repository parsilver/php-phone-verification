<?php

namespace Farzai\PhoneVerification\Repositories;

use Farzai\PhoneVerification\Entities\Verification;

interface VerificationRepository extends Repository
{
    /**
     * @param string $phoneNumber
     * @return Verification
     */
    public function create(string $phoneNumber): Verification;

    /**
     * @param string $phoneNumber
     * @param string $reference
     * @param string $code
     * @return Verification|null
     */
    public function findForValidate(string $phoneNumber, string $reference, string $code): ?Verification;

    /**
     * @param Verification $verifier
     */
    public function markAsExpired(Verification $verifier): Verification;

    /**
     * @param Verification $verifier
     */
    public function markAsVerified(Verification $verifier): Verification;
}