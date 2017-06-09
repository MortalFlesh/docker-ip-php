<?php

namespace MF\DockerIp\Service;

use MF\Collection\Generic\IList;
use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Entity\Net;

class NetResolver
{
    /**
     * @param Net[]|IList $nets
     * @return Ip
     */
    public function findSuitableIp(IList $nets): Ip
    {
        throw new \Exception(sprintf('Method %s is not implemented yet.', __METHOD__));
    }
}
