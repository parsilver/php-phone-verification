<?php

namespace Farzai\Tests;

use Farzai\PhoneVerification\Adapters\UrlAdapter;
use Farzai\PhoneVerification\Verifier;
use Farzai\Tests\Internal\MockEndpointTransform;
use Farzai\Tests\Internal\SMSMockProvider;
use Farzai\PhoneVerification\Entities;
use Farzai\Tests\Internal\VerificationTestRepository;

class SmsVerifyUrlTest extends TestCase
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
        $callbackUrl = 'https://app.com/callback?debug=1&app-version=1.0';
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new UrlAdapter($this->smsProvider, $repository);
        $adapter->setCallbackUrl($callbackUrl);

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->smsProvider->assertSent($phoneNumber);
        $this->assertEquals($phoneNumber, $entity->phone_number);
        $this->assertEquals($reference, $entity->reference);
        $this->assertEquals($code, $entity->code);

        $expected = "https://app.com/callback?debug=1&app-version=1.0&mobile={$entity->phone_number}&ref={$entity->reference}&code={$entity->code}";

        $this->smsProvider->assertSent($phoneNumber, function ($message) use ($expected) {
            return false !== strpos($message, $expected);
        });
    }

    public function test_should_be_create_verification_with_transform_endpoint_success()
    {
        $callbackUrl = 'https://app.com/callback?debug=1&app-version=1.0';
        $phoneNumber = "0988887777";

        $repository = new VerificationTestRepository();
        $repository->shouldUse(new Entities\Verification([
            'code' => $code = (string)rand(1000, 9999),
            'reference' => $reference = (string)rand(1000, 9999)
        ]));

        $adapter = new UrlAdapter($this->smsProvider, $repository);
        $adapter->setCallbackUrl($callbackUrl);
        $adapter->setEndpointTransform(new MockEndpointTransform());

        $verifier = new Verifier($adapter);
        $entity = $verifier->create($phoneNumber);

        $this->smsProvider->assertSent($phoneNumber);
        $this->assertEquals($phoneNumber, $entity->phone_number);
        $this->assertEquals($reference, $entity->reference);
        $this->assertEquals($code, $entity->code);

        $expected = "https://google.com";

        $this->smsProvider->assertSent($phoneNumber, function ($message) use ($expected) {
            return false !== strpos($message, $expected);
        });
    }

}