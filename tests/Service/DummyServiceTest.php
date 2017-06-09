<?php

namespace MF\DockerIp\Tests\Service;

use MF\DockerIp\Service\DummyService;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class DummyServiceTest extends TestCase
{
    public function testShouldPass()
    {
        $dummy = new DummyService();

        $this->assertSame('bar', $dummy->foo());
    }
}
