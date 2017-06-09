<?php

namespace MF\DockerIp\Entity;

use Assert\Assertion;

class Net
{
    const PATTERN_NAME = '^(\w+\d{1})';
    const REGEXP_NAME = '/' . self::PATTERN_NAME . '/';

    /** @var string */
    private $name;

    /** @var ?Ip */
    private $ip;

    /** @var ?bool */
    private $active;

    public function __construct(string $name, ?IP $ip, ?bool $active)
    {
        Assertion::regex($name, self::REGEXP_NAME, sprintf('Net name "%s" is not in valid format.', $name));

        $this->name = $name;
        $this->ip = $ip;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIp(): ?Ip
    {
        return $this->ip;
    }

    public function isActive(): bool
    {
        return $this->active && !empty($this->ip);
    }
}
