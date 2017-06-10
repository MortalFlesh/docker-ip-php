<?php

namespace MF\DockerIp\Command;

use Assert\Assertion;
use MF\Collection\Mutable\Enhanced\Map;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{
    const COMMAND_PREFIX = 'docker-ip:';

    /** @var SymfonyStyle */
    protected $io;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('MF/Docker-Ip runs just for you :)');
        $this->io->section($this->getName());
    }

    public function setName($name)
    {
        return parent::setName(self::COMMAND_PREFIX . $name);
    }

    protected function checkOptions(InputInterface $input, array $options): array
    {
        return Map::createFromArray($input->getOptions())
            ->filter(function ($key) use ($options) {
                return in_array($key, $options, true);
            })
            ->map(function ($key, $value) {
                Assertion::true(is_string($value), sprintf('Option "%s" is missing.', $key));
                Assertion::notEmpty($value, sprintf('Option "%s" must not be empty.', $key));

                return $value;
            })
            ->toArray();
    }
}
