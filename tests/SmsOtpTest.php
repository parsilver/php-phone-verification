<?php

namespace Farzai\Tests;

use Farzai\PhoneVerification\Adapters\SmsAdapter;
use Farzai\PhoneVerification\Entities;
use Farzai\PhoneVerification\Exceptions\InvalidVerificationException;
use Farzai\PhoneVerification\Exceptions\VerificationHasExpired;
use Farzai\PhoneVerification\Verifier;
use Farzai\Tests\Internal\SMSMockProvider;
use Farzai\Tests\Internal\VerificationTestRepository;


class SmsOtpTest extends TestCase
{
    /**
     * @var SMSMockProvider
     */
    private $smsProvider;


    protected function setUp(): void
    {
        parent::setUp();

        $this->smsProvider = new SMSMockProvider();
    }

    public function test_should_be_create_verification_success()
    {
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new SmsAdapter($this->smsProvider, $repository);

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->smsProvider->assertSent($phoneNumber);
        $this->assertEquals($phoneNumber, $entity->phone_number);
        $this->assertEquals($reference, $entity->reference);
        $this->assertEquals($code, $entity->code);

        $this->smsProvider->assertSent($phoneNumber, "The SMS-OTP is {$entity->code} ({$entity->reference}).");
    }


    public function test_should_be_verify_fail_when_expires()
    {
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new SmsAdapter($this->smsProvider, $repository);

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->smsProvider->assertSent($phoneNumber);
        $this->assertFalse($entity->isVerified());
        $this->assertFalse($entity->isExpired());

        $adapter->markAsExpired($entity);

        sleep(2);

        $this->expectException(VerificationHasExpired::class);
        $verifier->verify($phoneNumber, $entity->reference, $entity->code);
    }



    public function test_should_verify_fail_when_code_is_invalid()
    {
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new SmsAdapter($this->smsProvider, $repository);

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->assertEquals($code, $entity->code);
        $this->assertEquals($reference, $entity->reference);

        $this->smsProvider->assertSent($phoneNumber);

        $this->assertFalse($entity->isVerified());
        $this->assertFalse($entity->isExpired());

        // Mock for method findForValidate, This should return null if repository can't find data
        $repository->shouldUse(null);

        try {
            $entity = $verifier->verify($phoneNumber, $reference, "test-invalid-code");
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidVerificationException::class, $exception);
        }

        $this->assertFalse($entity->isVerified());
        $this->assertFalse($entity->isExpired());
    }


    public function test_should_be_verify_success()
    {
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new SmsAdapter($this->smsProvider, $repository);

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->smsProvider->assertSent($phoneNumber);
        $this->assertFalse($entity->isVerified());
        $this->assertFalse($entity->isExpired());

        $entity = $verifier->verify($phoneNumber, $reference, $code);

        $this->assertTrue($entity->isVerified());
        $this->assertTrue($entity->isExpired());
    }
}