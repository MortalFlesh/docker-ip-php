<?php

namespace MF\DockerIp\Entity;

class Net
{
    /** @var string */
    private $name;

    /** @var ?Ip */
    private $ip;

    /** @var ?bool */
    private $active;

    public function __construct(string $name, ?IP $ip, ?bool $active)
    {
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

    public function getActive(): bool
    {
        return $this->active && !empty($this->ip);
    }
}
