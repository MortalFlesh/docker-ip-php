<?php

namespace MF\DockerIp\tests\Service;

use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Service\Hosts;
use MF\DockerIp\Service\RegexHelper;
use MF\DockerIp\Tests\AbstractTestCase;

class HostsTest extends AbstractTestCase
{
    const SUITABLE_IP = '10.1.10.1';

    /** @var Hosts */
    private $hosts;

    public function setUp()
    {
        $this->hosts = new Hosts(
            new RegexHelper()
        );
    }

    public function testShouldReplaceOriginalDomainLineBySuitableDockerIp()
    {
        $path = __DIR__ . '/../Fixtures/';

        $ip = new Ip(self::SUITABLE_IP);
        $hostsPath = $this->copyFile($path, 'hosts_original', 'hosts');
        $domain = 'your_domain';

        $this->hosts->replace($hostsPath, $domain, $ip);

        $this->assertFileEquals($path . 'hosts_updated', $hostsPath);
    }
}
