<?php

namespace MF\DockerIp\Command;

use MF\DockerIp\Facade\DistributeIpToHostFacade;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DistributeIpToHostCommand extends AbstractCommand
{
    const PLACEHOLDER = 'DOCKER_IP_PLACEHOLDER';
    const OPTIONS = ['domain', 'docker-file', 'hosts', 'placeholder'];

    /** @var DistributeIpToHostFacade */
    private $facade;

    public function __construct(DistributeIpToHostFacade $distributeIpToHost)
    {
        $this->facade = $distributeIpToHost;

        parent::__construct('distributeIpToHost');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('...')
            ->addOption('domain', 'd', InputOption::VALUE_REQUIRED, 'Your local domain')
            ->addOption('docker-file', null, InputOption::VALUE_REQUIRED, 'Full path to your docker compose yml')
            ->addOption(
                'hosts',
                null,
                InputOption::VALUE_OPTIONAL,
                'Full path to your hosts file',
                '/etc/hosts'
            )
            ->addOption(
                'placeholder',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Placeholder used in DOCKER_FILE',
                self::PLACEHOLDER
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            list(
                'domain' => $domain,
                'docker-file' => $dockerFilePath,
                'hosts' => $hostsPath,
                'placeholder' => $placeholder
                ) = $this->checkOptions($input, self::OPTIONS);

            $this->facade->distributeIpToHost($domain, $dockerFilePath, $hostsPath, $placeholder);

            if ($this->io->isVerbose()) {
                $this->io->note(sprintf('IP: %s', $this->facade->getIp()));
            }

            $this->io->success('Done');

            return 0;
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());

            return $e->getCode() ?? 1;
        }
    }
}
