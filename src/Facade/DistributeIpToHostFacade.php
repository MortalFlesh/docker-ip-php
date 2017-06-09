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

    public function distributeIpToHost(string $domain, string $dockerFilePath, string $hostsPath, string $placeholder)
    {
        $nets = $this->ifConfig->getNets();
        $this->ip = $this->netsResolver->findSuitableIp($nets);

        $this->hosts->replace($hostsPath, $domain, $this->ip);
        $this->dockerFile->replace($dockerFilePath, $placeholder);
    }

    public function getIp(): Ip
    {
        return $this->ip;
    }
}
