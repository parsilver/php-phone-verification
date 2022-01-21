<?php

namespace Farzai\PhoneVerification\Adapters;

use Farzai\PhoneVerification\Entities\Verification;
use Farzai\PhoneVerification\Repositories\VerificationRepository;
use Farzai\PhoneVerification\SMS\Provider;

class SmsAdapter implements VerificationRepository
{
    /**
     * @var Provider
     */
    protected Provider $sms;

    /**
     * @var VerificationRepository
     */
    protected VerificationRepository $repository;

    /**
     * @var callable[]
     */
    protected $before = [];

    /**
     * @var callable[]
     */
    protected $after = [];

    /**
     * @var callable|null
     */
    protected $createMessage;

    /**
     * @param Provider $sms
     * @param VerificationRepository $repository
     */
    public function __construct(Provider $sms, VerificationRepository $repository)
    {
        $this->sms = $sms;
        $this->repository = $repository;
    }

    /**
     * Create message
     *
     * @param callable $handler
     * @return $this
     */
    public function createMessageUsing(callable $handler)
    {
        $this->createMessage = $handler;

        return $this;
    }

    /**
     * Handle before send SMS
     *
     */
    public function beforeSend(callable $listener)
    {
        $this->before[] = $listener;

        return $this;
    }

    /**
     * Handle after send SMS
     */
    public function afterSend(callable $listener)
    {
        $this->after[] = $listener;

        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return Verification
     */
    public function create(string $phoneNumber): Verification
    {
        $entity = $this->repository->create($phoneNumber);

        $this->fireBefore($entity);

        $result = $this->sms->send(
            $entity->phone_number, $this->createMessage($entity)
        );

        $this->fireAfter($entity, $result);

        return $entity;
    }

    /**
     * @param Verification $entity
     * @return string
     */
    protected function createMessage(Verification $entity): string
    {
        return ($this->createMessage ?: function ($entity) {
            return "The SMS-OTP is {$entity->code} ({$entity->reference}).";
        })($entity);
    }


    /**
     * @param string $phoneNumber
     * @param string $reference
     * @param string $code
     * @return Verification|null
     */
    public function findForValidate(string $phoneNumber, string $reference, string $code): ?Verification
    {
        return $this->repository->findForValidate($phoneNumber, $reference, $code);
    }

    /**
     * @param Verification $verifier
     * @return Verification
     */
    public function markAsExpired(Verification $verifier): Verification
    {
        return $this->repository->markAsExpired($verifier);
    }

    /**
     * @param Verification $verifier
     * @return Verification
     */
    public function markAsVerified(Verification $verifier): Verification
    {
        return $this->repository->markAsVerified($verifier);
    }

    /**
     * @param Verification $entity
     * @return void
     */
    protected function fireBefore(Verification $entity)
    {
        foreach ($this->before as $listener) {
            $listener($entity);
        }
    }

    /**
     * @param Verification $entity
     * @param $result
     * @return void
     */
    protected function fireAfter(Verification $entity, $result = null)
    {
        foreach ($this->after as $listener) {
            $listener($entity, $result);
        }
    }
}