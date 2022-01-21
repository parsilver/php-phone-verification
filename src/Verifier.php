<?php

namespace Farzai\PhoneVerification;

use Farzai\PhoneVerification\Entities\Verification;
use Farzai\PhoneVerification\Exceptions\VerificationException;
use Farzai\PhoneVerification\Repositories\VerificationRepository;

class Verifier
{
    /**
     * @var VerificationRepository
     */
    private VerificationRepository $verificationRepository;

    /**
     * @param VerificationRepository $verificationRepository
     */
    public function __construct(VerificationRepository $verificationRepository)
    {
        $this->verificationRepository = $verificationRepository;
    }

    /**
     * @param string $phoneNumber
     * @return void
     */
    public function create(string $phoneNumber): Verification
    {
        return $this->verificationRepository->create($phoneNumber);
    }

    /**
     * @param $phoneNumber
     * @param $reference
     * @param $code
     * @return Verification
     * @throws Exceptions\InvalidVerificationException
     * @throws Exceptions\VerificationHasExpired
     */
    public function verify($phoneNumber, $reference, $code): Verification
    {
        $entity = $this->verificationRepository->findForValidate($phoneNumber, $reference, $code);

        if (! $entity) {
            throw VerificationException::invalid();
        }

        if ($entity->isExpired()) {
            $this->verificationRepository->markAsExpired($entity);

            throw VerificationException::expired();
        }

        return $this->verificationRepository->markAsVerified($entity);
    }
}