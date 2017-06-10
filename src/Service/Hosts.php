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

    /** @var StringHelper */
    private $stringHelper;

    public function __construct(RegexHelper $regexHelper, StringHelper $stringHelper)
    {
        $this->regexHelper = $regexHelper;
        $this->stringHelper = $stringHelper;
    }

    public function replace(string $hostsPath, string $domain, Ip $ip, bool $dryRun = false): void
    {
        $this->checkFile($hostsPath);

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

        if (!$dryRun) {
            file_put_contents($hostsPath, implode("\n", $newHostLines->toArray()));
        }
    }

    private function checkFile(string $hostsPath): void
    {
        Assertion::file($hostsPath, sprintf('Hosts file "%s" is not found.', $hostsPath));
        Assertion::writeable($hostsPath, sprintf('Hosts file "%s" is not writeable.', $hostsPath));
    }

    private function isDomainLine(string $line, string $domain): bool
    {
        return $this->regexHelper->contains($line, sprintf('/127\.0\.0\.1\s+(%s){1}$/', $domain));
    }

    public function revert(string $hostsPath, bool $dryRun = false): void
    {
        $this->checkFile($hostsPath);

        $hostsContent = file_get_contents($hostsPath);
        $revertedHostLines = new ListCollection('string');

        $lines = ListCollection::createGenericListFromArray('string', explode("\n", $hostsContent));

        $ignoreLine = false;
        $lines->each(function (string $line) use ($revertedHostLines, &$ignoreLine): void {
            if ($ignoreLine) {
                $ignoreLine = false;

                return;
            }

            if ($this->isChangedLine($line)) {
                $line = str_replace(self::PLACEHOLDER, '', $line);
                $ignoreLine = true;
            }

            $revertedHostLines->add($line);
        });

        Assertion::lessThan(
            $revertedHostLines->count(),
            $lines->count(),
            sprintf('Changes was not reverted in "%s" file.', $hostsPath)
        );

        if (!$dryRun) {
            file_put_contents($hostsPath, implode("\n", $revertedHostLines->toArray()));
        }
    }

    private function isChangedLine(string $line): bool
    {
        return $this->stringHelper->contains($line, self::PLACEHOLDER);
    }
}
