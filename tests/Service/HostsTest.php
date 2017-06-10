<?php

namespace MF\DockerIp\tests\Service;

use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Service\Hosts;
use MF\DockerIp\Service\RegexHelper;
use MF\DockerIp\Service\StringHelper;
use MF\DockerIp\Tests\AbstractTestCase;

class HostsTest extends AbstractTestCase
{
    const SUITABLE_IP = '10.1.10.1';

    /** @var Hosts */
    private $hosts;

    public function setUp()
    {
        $this->hosts = new Hosts(
            new RegexHelper(),
            new StringHelper()
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

    public function testShouldNOTReplaceOriginalDomainLineBySuitableDockerIpOnDryRun()
    {
        $path = __DIR__ . '/../Fixtures/';

        $ip = new Ip(self::SUITABLE_IP);
        $hostsPath = $this->copyFile($path, 'hosts_original', 'hosts');
        $domain = 'your_domain';

        $this->hosts->replace($hostsPath, $domain, $ip, true);

        $this->assertFileNotEquals($path . 'hosts_updated', $hostsPath);
        $this->assertFileEquals($path . 'hosts_original', $hostsPath);
    }

    public function testShouldRevertChanges()
    {
        $path = __DIR__ . '/../Fixtures/';
        $hostsPath = $this->copyFile($path, 'hosts_updated', 'hosts');

        $this->hosts->revert($hostsPath);

        $this->assertFileEquals($path . 'hosts_original', $hostsPath);
    }

    public function testShouldNOTRevertChangesOnDryRun()
    {
        $path = __DIR__ . '/../Fixtures/';
        $hostsPath = $this->copyFile($path, 'hosts_updated', 'hosts');

        $this->hosts->revert($hostsPath, true);

        $this->assertFileNotEquals($path . 'hosts_original', $hostsPath);
        $this->assertFileEquals($path . 'hosts_updated', $hostsPath);
    }
}
