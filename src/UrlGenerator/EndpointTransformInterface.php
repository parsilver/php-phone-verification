<?php

namespace Farzai\PhoneVerification\UrlGenerator;

use Farzai\PhoneVerification\Entities\Verification;

interface EndpointTransformInterface
{
    /**
     * @param string $endpoint
     * @param Verification $entity
     * @return string
     */
    public function transform(string $endpoint, Verification $entity): string;
}