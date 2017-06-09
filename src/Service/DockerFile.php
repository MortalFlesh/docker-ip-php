<?php

namespace MF\DockerIp\Service;

use MF\DockerIp\Entity\Ip;

class DockerFile
{
    public function replace(string $dockerFilePath, string $placeholder, Ip $ip): void
    {
        throw new \Exception(sprintf('Method %s is not implemented yet.', __METHOD__));
    }
}
