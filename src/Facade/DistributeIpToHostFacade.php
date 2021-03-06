<?php

namespace MF\DockerIp\Facade;

use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Service\DockerFile;
use MF\DockerIp\Service\Hosts;
use MF\DockerIp\Service\IfConfig;
use MF\DockerIp\Service\NetResolver;

class DistributeIpToHostFacade
{
    /** @var IfConfig */
    private $ifConfig;

    /** @var NetResolver */
    private $netsResolver;

    /** @var Hosts */
    private $hosts;

    /** @var DockerFile */
    private $dockerFile;

    /** @var Ip */
    private $ip;

    public function __construct(IfConfig $ifConfig, NetResolver $netsResolver, Hosts $hosts, DockerFile $dockerFile)
    {
        $this->ifConfig = $ifConfig;
        $this->netsResolver = $netsResolver;
        $this->hosts = $hosts;
        $this->dockerFile = $dockerFile;
    }

    public function distributeIpToHost(string $domain, string $hostsPath, string $dockerFilePath, string $placeholder)
    {
        $nets = $this->ifConfig->getNets();
        $this->ip = $this->netsResolver->findSuitableIp($nets);

        // dry run first
        $this->hosts->replace($hostsPath, $domain, $this->ip, true);
        $this->dockerFile->replace($dockerFilePath, $placeholder, $this->ip, true);

        // real run
        $this->hosts->replace($hostsPath, $domain, $this->ip);
        $this->dockerFile->replace($dockerFilePath, $placeholder, $this->ip);
    }

    public function getIp(): Ip
    {
        return $this->ip;
    }

    public function revert(string $hostsPath, string $dockerFilePath): void
    {
        // dry run first
        $this->hosts->revert($hostsPath, true);
        $this->dockerFile->revert($dockerFilePath, true);

        // real run
        $this->hosts->revert($hostsPath);
        $this->dockerFile->revert($dockerFilePath);
    }
}
