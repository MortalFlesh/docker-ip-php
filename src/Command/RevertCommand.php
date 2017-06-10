<?php

namespace MF\DockerIp\Command;

use MF\DockerIp\Facade\DistributeIpToHostFacade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RevertCommand extends AbstractCommand
{
    const OPTIONS = ['docker-file', 'hosts'];

    /** @var DistributeIpToHostFacade */
    private $facade;

    public function __construct(DistributeIpToHostFacade $facade)
    {
        $this->facade = $facade;

        parent::__construct('revert');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reverts changes from `distributeIpToHost` in hosts and docker file')
            ->addOption('docker-file', null, InputOption::VALUE_REQUIRED, 'Full path to your docker compose yml')
            ->addOption(
                'hosts',
                null,
                InputOption::VALUE_OPTIONAL,
                'Full path to your hosts file',
                '/etc/hosts'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            list(
                'docker-file' => $dockerFilePath,
                'hosts' => $hostsPath,
                ) = $this->checkOptions($input, self::OPTIONS);

            $this->facade->revert($hostsPath, $dockerFilePath);

            $this->io->success('Done');

            return 0;
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return $e->getCode() ?? 1;
        }
    }
}
