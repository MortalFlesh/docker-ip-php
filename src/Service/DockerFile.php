<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;
use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;

class DockerFile
{
    const REPLACED_PLACEHOLDER = '#REPLACED_BY_DOCKER_IP ';

    /** @var StringHelper */
    private $stringHelper;

    public function __construct(StringHelper $stringHelper)
    {
        $this->stringHelper = $stringHelper;
    }

    public function replace(string $dockerFilePath, string $placeholder, Ip $ip, bool $dryRun = false): void
    {
        $this->checkFile($dockerFilePath);

        $dockerFileContents = file_get_contents($dockerFilePath);
        $newDockerFileLines = new ListCollection('string');

        $lines = ListCollection::createGenericListFromArray('string', explode("\n", $dockerFileContents));
        $lines->each(function (string $line) use ($placeholder, $newDockerFileLines, $ip): void {
            if ($this->containsPlaceholder($line, $placeholder)) {
                $replacedLine = self::REPLACED_PLACEHOLDER . $line;
                $newDockerFileLines->add($replacedLine);

                $line = str_replace($placeholder, $ip->getValue(), $line);
            }

            $newDockerFileLines->add($line);
        });

        Assertion::greaterThan(
            $newDockerFileLines->count(),
            $lines->count(),
            sprintf('Placeholder "%s" was not replaced in "%s" file.', $placeholder, $dockerFilePath)
        );

        if (!$dryRun) {
            file_put_contents($dockerFilePath, implode("\n", $newDockerFileLines->toArray()));
        }
    }

    private function checkFile(string $dockerFilePath): void
    {
        Assertion::file($dockerFilePath, sprintf('Docker file "%s" is not found.', $dockerFilePath));
        Assertion::writeable($dockerFilePath, sprintf('Docker file "%s" is not writeable.', $dockerFilePath));
    }

    private function containsPlaceholder(string $line, string $placeholder): bool
    {
        return $this->stringHelper->contains($line, $placeholder);
    }

    public function revert(string $dockerFilePath, bool $dryRun = false): void
    {
        $this->checkFile($dockerFilePath);

        $dockerFileContents = file_get_contents($dockerFilePath);
        $revertedDockerLines = new ListCollection('string');

        $lines = ListCollection::createGenericListFromArray('string', explode("\n", $dockerFileContents));

        $ignoreLine = false;
        $lines->each(function (string $line) use ($revertedDockerLines, &$ignoreLine): void {
            if ($ignoreLine) {
                $ignoreLine = false;

                return;
            }

            if ($this->isChangedLine($line)) {
                $line = str_replace(self::REPLACED_PLACEHOLDER, '', $line);
                $ignoreLine = true;
            }

            $revertedDockerLines->add($line);
        });

        Assertion::lessThan(
            $revertedDockerLines->count(),
            $lines->count(),
            sprintf('Changes was not reverted in "%s" file.', $dockerFilePath)
        );

        if (!$dryRun) {
            file_put_contents($dockerFilePath, implode("\n", $revertedDockerLines->toArray()));
        }
    }

    private function isChangedLine(string $line): bool
    {
        return $this->stringHelper->contains($line, self::REPLACED_PLACEHOLDER);
    }
}
