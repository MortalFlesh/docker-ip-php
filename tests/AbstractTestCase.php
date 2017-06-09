<?php

namespace MF\DockerIp\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
}
