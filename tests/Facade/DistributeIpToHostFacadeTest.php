<?php

namespace MF\DockerIp\Tests\Facade;

use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Entity\Net;
use MF\DockerIp\Facade\DistributeIpToHostFacade;
use MF\DockerIp\Service\DockerFile;
use MF\DockerIp\Service\Hosts;
use MF\DockerIp\Service\IfConfig;
use MF\DockerIp\Service\NetResolver;
use MF\DockerIp\Tests\AbstractTestCase;
use Mockery as m;

class DistributeIpToHostFacadeTest extends AbstractTestCase
{
    /** @var DistributeIpToHostFacade */
    private $facade;

    /** @var IfConfig|m\MockInterface */
    private $ifConfig;

    /** @var NetResolver|m\MockInterface */
    private $netResolver;

    /** @var Hosts|m\MockInterface */
    private $hosts;

    /** @var DockerFile|m\MockInterface */
    private $dockerFile;

    public function setUp()
    {
        $this->ifConfig = m::mock(IfConfig::class);
        $this->netResolver = m::mock(NetResolver::class);
        $this->hosts = m::spy(Hosts::class);
        $this->dockerFile = m::spy(DockerFile::class);

        $this->facade = new DistributeIpToHostFacade(
            $this->ifConfig,
            $this->netResolver,
            $this->hosts,
            $this->dockerFile
        );
    }

    public function testShouldDistributeIpToHost()
    {
        $domain = 'your_domain';
        $dockerFilePath = '/myapp/docker-compose.yml';
        $hostsPath = '/etc/hosts';
        $placeholder = 'DOCKER_IP_PLACEHOLDER';

        $nets = new ListCollection(Net::class);
        $expectedIp = new Ip('127.0.0.1');

        $this->ifConfig->shouldReceive('getNets')
            ->once()
            ->andReturn($nets);

        $this->netResolver->shouldReceive('findSuitableIp')
            ->with($nets)
            ->andReturn($expectedIp);

        $this->facade->distributeIpToHost($domain, $dockerFilePath, $hostsPath, $placeholder);
        $ip = $this->facade->getIp();

        $this->hosts->shouldHaveReceived('replace')
            ->with($hostsPath, $domain, $expectedIp)
            ->once();

        $this->dockerFile->shouldHaveReceived('replace')
            ->with($dockerFilePath, $placeholder, $expectedIp)
            ->once();

        $this->assertSame($expectedIp, $ip);
    }
}
