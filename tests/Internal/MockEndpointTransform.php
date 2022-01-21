<?php

namespace Farzai\Tests\Internal;

use Farzai\PhoneVerification\Entities\Verification;
use Farzai\PhoneVerification\UrlGenerator\EndpointTransformInterface;

class MockEndpointTransform implements EndpointTransformInterface
{
    /**
     * @param string $endpoint
     * @param Verification $entity
     * @return string
     */
    public function transform(string $endpoint, Verification $entity): string
    {
        return "https://google.com";
    }
}