<?php

namespace MF\DockerIp\tests\Service;

use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Entity\Net;
use MF\DockerIp\Service\IfConfig;
use MF\DockerIp\Service\RegexHelper;
use MF\DockerIp\Tests\AbstractTestCase;
use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class IfConfigTest extends AbstractTestCase
{
    /** @var IfConfig */
    private $ifConfig;

    /** @var ProcessBuilder|m\MockInterface */
    private $processBuilder;

    public function setUp()
    {
        $this->processBuilder = m::mock(ProcessBuilder::class);

        $this->ifConfig = new IfConfig(
            $this->processBuilder,
            new RegexHelper()
        );
    }

    /**
     * @param string $ifConfigFile
     * @param Net[] $expectedNets
     *
     * @dataProvider ifConfigProvider
     */
    public function testShouldParseIfConfig(string $ifConfigFile, array $expectedNets)
    {
        $ifConfigProcess = m::spy(Process::class);
        $ifConfigProcess->shouldReceive('getOutput')
            ->once()
            ->andReturn(file_get_contents(__DIR__ . '/../Fixtures/' . $ifConfigFile));

        $this->processBuilder->shouldReceive('setArguments')
            ->with(['ifconfig'])
            ->once()
            ->andReturn($this->processBuilder);
        $this->processBuilder->shouldReceive('getProcess')
            ->once()
            ->andReturn($ifConfigProcess);

        $nets = $this->ifConfig->getNets();

        $ifConfigProcess->shouldHaveReceived('run')->once();

        $this->assertEquals($expectedNets, $nets->toArray());
    }

    public function ifConfigProvider(): array
    {
        return [
            // file, expetedNets
            'empty' => ['ifconfig_empty', []],
            'full' => [
                'ifconfig_full',
                [
                    new Net('en0', new Ip('10.1.1.11'), true),
                    new Net('en1', null, false),
                    new Net('vboxnet0', new Ip('192.168.100.1'), null),
                    new Net('en2', new Ip('10.1.10.15'), true),
                ],
            ],
            'no-net' => [
                'ifconfig_no_net',
                [
                    new Net('en0', null, false),
                    new Net('en1', null, false),
                    new Net('vboxnet0', new Ip('192.168.100.1'), null),
                ],
            ],
        ];
    }
}
