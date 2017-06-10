<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;
use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;

class Hosts
{
    const PLACEHOLDER = '#REPLACED_BY_DOCKER_IP ';

    /** @var RegexHelper */
    private $regexHelper;

    public function __construct(RegexHelper $regexHelper)
    {
        $this->regexHelper = $regexHelper;
    }

    public function replace(string $hostsPath, string $domain, Ip $ip): void
    {
        Assertion::file($hostsPath, sprintf('Hosts file "%s" is not found.', $hostsPath));

        $hostsContent = file_get_contents($hostsPath);
        $newHostLines = new ListCollection('string');

        $lines = ListCollection::createGenericListFromArray('string', explode("\n", $hostsContent));
        $lines->each(function (string $line) use ($domain, $newHostLines, $ip): void {
            if ($this->isDomainLine($line, $domain)) {
                $replacedLine = self::PLACEHOLDER . $line;
                $newHostLines->add($replacedLine);

                $line = str_replace('127.0.0.1', $ip->getValue(), $line);
            }

            $newHostLines->add($line);
        });

        Assertion::greaterThan(
            $newHostLines->count(),
            $lines->count(),
            sprintf('Domain "%s" was not replaced in "%s" file.', $domain, $hostsPath)
        );

        file_put_contents($hostsPath, implode("\n", $newHostLines->toArray()));
    }

    private function isDomainLine(string $line, string $domain): bool
    {
        return $this->regexHelper->contains($line, sprintf('/127\.0\.0\.1\s+(%s){1}$/', $domain));
    }
}
