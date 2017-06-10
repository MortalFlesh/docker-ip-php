<?php

namespace MF\DockerIp\Service;

use MF\Collection\Generic\IList;
use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Entity\Net;
use Symfony\Component\Process\ProcessBuilder;

class IfConfig
{
    const REGEXP_NET_NAME = '/' . Net::PATTERN_NAME . ':/';
    const REGEXP_INET_IP = '/inet (' . Ip::PATTERN . ')/';
    const REGEXP_ACTIVE = '/status: (active|inactive)?/';

    /** @var ProcessBuilder */
    private $processBuilder;

    /** @var RegexHelper */
    private $regexHelper;

    public function __construct(ProcessBuilder $processBuilder, RegexHelper $regexHelper)
    {
        $this->processBuilder = $processBuilder;
        $this->regexHelper = $regexHelper;
    }

    /**
     * @return Net[]|IList<Net>
     */
    public function getNets(): IList
    {
        $data = $this->runIfConfig();

        return $this->parseData($data);
    }

    private function runIfConfig(): string
    {
        $ifConfigProcess = $this->processBuilder
            ->setArguments(['ifconfig'])
            ->getProcess();

        $ifConfigProcess->run();

        return $ifConfigProcess->getOutput();
    }

    /**
     * @param string $data
     * @return Net[]|IList<Net>
     */
    private function parseData(string $data): IList
    {
        $nets = new ListCollection(Net::class);
        $netParts = [];

        ListCollection::createGenericListFromArray('string', explode("\n", $data))
            ->each(function (string $line) use ($nets, &$netParts): void {
                if ($this->lineContains($line, self::REGEXP_NET_NAME)) {
                    if (!empty($netParts)) {
                        $this->addNet($netParts, $nets);
                        $netParts = [];
                    }

                    $netParts['name'] = $this->parseLine($line, self::REGEXP_NET_NAME);
                } elseif ($this->lineContains($line, self::REGEXP_INET_IP)) {
                    $netParts['ip'] = new Ip($this->parseLine($line, self::REGEXP_INET_IP));
                } elseif ($this->lineContains($line, self::REGEXP_ACTIVE)) {
                    $netParts['active'] = $this->parseLine($line, self::REGEXP_ACTIVE) === 'active';
                }
            });

        if (!empty($netParts)) {
            $this->addNet($netParts, $nets);
        }

        return $nets;
    }

    private function addNet(array $netParts, IList $nets): void
    {
        $net = new Net(
            $netParts['name'] ?? '',
            $netParts['ip'] ?? null,
            $netParts['active'] ?? null
        );
        $nets->add($net);
    }

    private function lineContains(string $line, string $pattern): bool
    {
        return $this->regexHelper->contains($line, $pattern);
    }

    private function parseLine(string $line, string $pattern): string
    {
        return $this->regexHelper->parse($line, $pattern);
    }
}
