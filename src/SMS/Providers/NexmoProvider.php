<?php

namespace Farzai\PhoneVerification\SMS\Providers;

use Farzai\PhoneVerification\SMS\Provider;
use GuzzleHttp\Client as GuzzleHttp;

class NexmoProvider implements Provider
{
    /**
     * @var array
     */
    private array $config;

    /**
     * @var string
     */
    private $endpoint = 'https://nexmo-nexmo-messaging-v1.p.rapidapi.com/send-sms';

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $phoneNumber, string $message, array $options = [])
    {
        $client = new GuzzleHttp();

        return $client->post($this->endpoint, [
            'headers' => [
                'x-rapidapi-host' => 'nexmo-nexmo-messaging-v1.p.rapidapi.com',
                'x-rapidapi-key' => $this->config['key'],
            ],
            'json' => [
                'from' => $this->config['from'],
                'to' => $phoneNumber,
                'text' => $message,
            ]
        ]);
    }
}