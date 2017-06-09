<?php

namespace MF\DockerIp\Entity;

use Assert\Assertion;

class Ip
{
    const PATTERN = '\b(?:\d{1,3}\.){3}\d{1,3}\b';
    const REGEXP = '/' . self::PATTERN . '/';

    /** @var string */
    private $ip;

    public function __construct(string $ip)
    {
        Assertion::regex($ip, self::REGEXP, sprintf('Invalid IP address given "%s".', $ip));
        $this->ip = $ip;
    }

    public function getValue(): string
    {
        return $this->ip;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
