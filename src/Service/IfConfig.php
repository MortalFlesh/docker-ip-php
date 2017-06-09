<?php

namespace MF\DockerIp\Service;

use MF\Collection\Generic\IList;
use MF\DockerIp\Entity\Net;

class IfConfig
{
    /**
     * @return Net[]|IList<Net>
     */
    public function getNets(): IList
    {
        throw new \Exception(sprintf('Method %s is not implemented yet.', __METHOD__));
    }
}
