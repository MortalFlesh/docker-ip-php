<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;
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
        Assertion::false($nets->isEmpty(), 'There are no nets to choose from.');

        $ip = $nets
            ->filter('($net) => $net->isActive()')
            ->first();

        Assertion::notEmpty($ip, 'There are no active nets to choose from.');

        return $ip->getIp();
    }
}
