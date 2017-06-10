<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;
use MF\Collection\Mutable\Generic\ListCollection;
use MF\DockerIp\Entity\Ip;

class DockerFile
{
    const PLACEHOLDER = '#REPLACED_BY_DOCKER_IP ';

    public function replace(string $dockerFilePath, string $placeholder, Ip $ip, bool $dryRun = false): void
    {
        Assertion::file($dockerFilePath, sprintf('Docker file "%s" is not found.', $dockerFilePath));

        $dockerFileContents = file_get_contents($dockerFilePath);
        $newDockerFileLines = new ListCollection('string');

        $lines = ListCollection::createGenericListFromArray('string', explode("\n", $dockerFileContents));
        $lines->each(function (string $line) use ($placeholder, $newDockerFileLines, $ip): void {
            if ($this->containsPlaceholder($line, $placeholder)) {
                $replacedLine = self::PLACEHOLDER . $line;
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

    private function containsPlaceholder(string $line, string $placeholder): bool
    {
        return strpos($line, $placeholder) !== false;
    }

    public function revert(string $dockerFilePath, bool $dryRun = false): void
    {
        throw new \Exception(sprintf('Method %s is not implemented yet.', __METHOD__));
    }
}
