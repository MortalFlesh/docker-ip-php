<?php

namespace MF\DockerIp\tests\Service;

use Assert\InvalidArgumentException;
use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Entity\Net;
use MF\DockerIp\Service\NetResolver;
use MF\DockerIp\Tests\AbstractTestCase;

class NetResolverTest extends AbstractTestCase
{
    /** @var NetResolver */
    private $netResolver;

    public function setUp()
    {
        $this->netResolver = new NetResolver();
    }

    /**
     * @param Net [] $nets
     * @param Ip $expectedIp
     *
     * @dataProvider suitableIpProvider
     */
    public function testShouldResolveSuitableIp(array $nets, Ip $expectedIp)
    {
        $netList = ListCollection::createGenericListFromArray(Net::class, $nets);

        $ip = $this->netResolver->findSuitableIp($netList);

        $this->assertSame($expectedIp, $ip);
    }

    public function suitableIpProvider(): array
    {
        $expectedIp = new Ip('10.1.1.11');

        return [
            // nets, expectedIp
            'first from more' => [
                [
                    new Net('en0', $expectedIp, true),
                    new Net('en1', null, false),
                    new Net('vboxnet0', new Ip('192.168.100.1'), null),
                    new Net('en2', new Ip('10.1.10.15'), true),
                ],
                $expectedIp,
            ],
            'only one' => [
                [
                    new Net('en0', new Ip('10.1.10.15'), false),
                    new Net('en1', null, false),
                    new Net('en2', null, true),
                    new Net('vboxnet0', new Ip('192.168.100.1'), null),
                    new Net('en3', $expectedIp, true),
                ],
                $expectedIp,
            ],
        ];
    }

    /**
     * @param Net[] $nets
     *
     * @dataProvider noSuitableIpProvider
     */
    public function testShouldThrowExceptionWhenNoSuitableIpFound(array $nets)
    {
        $netList = ListCollection::createGenericListFromArray(Net::class, $nets);

        $this->expectException(InvalidArgumentException::class);

        $this->netResolver->findSuitableIp($netList);
    }

    public function noSuitableIpProvider(): array
    {
        return [
            // nets
            'empty' => [[]],
            'none from list' => [
                [
                    new Net('en0', null, false),
                    new Net('en1', null, false),
                    new Net('vboxnet0', new Ip('192.168.100.1'), null),
                ],
            ],
        ];
    }
}
