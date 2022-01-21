<?php

namespace Farzai\Tests\Internal;

use Farzai\PhoneVerification\SMS\Provider;
use PHPUnit\Framework\Assert as PHPUnit;

class SMSMockProvider implements Provider
{
    /**
     * All of the messages that have been sent.
     *
     * @var array
     */
    private $messages = [];

    /**
     * @param string $phoneNumber
     * @param string $message
     * @param array $options
     * @return mixed|void
     */
    public function send(string $phoneNumber, string $message, array $options = [])
    {
        $this->messages[$phoneNumber][] = $message;
    }

    /**
     * Assert if a message was sent
     *
     * @param $phoneNumber
     * @param string|callable|null $expectMessage
     * @return void
     */
    public function assertSent($phoneNumber, $expectMessage = null)
    {
        $message = "The expected [{$phoneNumber}] SMS was not sent.";

        PHPUnit::assertTrue(
            $this->hasSent($phoneNumber, $expectMessage), $message
        );
    }

    /**
     * @param $phoneNumber
     * @param string|callable|null $expectMessage
     * @return bool
     */
    public function hasSent($phoneNumber, $expectMessage = null)
    {
        $sent = isset($this->messages[$phoneNumber]);

        if ($sent && ! is_null($expectMessage)) {
            foreach ($this->messages[$phoneNumber] as $message) {
                if (is_callable($expectMessage)) {
                    return true === $expectMessage($message);
                }

                if ($expectMessage === $message) {
                    return true;
                }
            }
        }

        return $sent;
    }
}