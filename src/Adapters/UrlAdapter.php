<?php

namespace Farzai\PhoneVerification\Adapters;

use Farzai\PhoneVerification\Entities\Verification;
use Farzai\PhoneVerification\UrlGenerator\EndpointTransformInterface;
use Farzai\PhoneVerification\UrlGenerator\URL;

class UrlAdapter extends SmsAdapter
{
    /**
     * @var string
     */
    protected $callbackUrl;

    /**
     * @var EndpointTransformInterface|null
     */
    protected $transformation;

    /**
     * @param Verification $entity
     * @return string
     */
    protected function getCallbackUrl(Verification $entity): string
    {
        return $this->callbackUrl ?: "";
    }

    /**
     * @param string $callback
     * @return $this
     */
    public function setCallbackUrl(string $callback)
    {
        $this->callbackUrl = $callback;

        return $this;
    }

    /**
     * @param EndpointTransformInterface $transformation
     * @return $this
     */
    public function setEndpointTransform(EndpointTransformInterface $transformation)
    {
        $this->transformation = $transformation;

        return $this;
    }

    /**
     * @param Verification $entity
     * @return string
     */
    protected function createMessage(Verification $entity): string
    {
        return ($this->createMessage ?: function ($entity) {
            $endpoint = $this->generateEndpoint($entity);
            return "Please click the link to verify: {$endpoint}";
        })($entity);
    }

    /**
     * @param $entity
     * @return string
     */
    protected function generateEndpoint($entity)
    {
        $endpoint = URL::parse($this->getCallbackUrl($entity))
            ->append($this->getQueryParams($entity))
            ->build();

        return $this->transformEndpoint($endpoint, $entity);
    }

    /**
     * @param string $endpoint
     * @param Verification $entity
     * @return string
     */
    protected function transformEndpoint(string $endpoint, Verification $entity): string
    {
        return $this->transformation
            ? $this->transformation->transform($endpoint, $entity)
            : $endpoint;
    }


    /**
     * @param Verification $entity
     * @return array
     */
    protected function getQueryParams(Verification $entity)
    {
        return [
            'mobile' => $entity->phone_number,
            'ref' => $entity->reference,
            'code' => $entity->code,
        ];
    }
}