<?php

namespace MF\DockerIp\tests\Service;

use MF\DockerIp\Entity\Ip;
use MF\DockerIp\Service\DockerFile;
use MF\DockerIp\Tests\AbstractTestCase;

class DockerFileTest extends AbstractTestCase
{
    const PLACEHOLDER = 'DOCKER_IP_PLACEHOLDER';
    const SUITABLE_IP = '10.1.10.1';

    /** @var DockerFile */
    private $dockerFile;

    public function setUp()
    {
        $this->dockerFile = new DockerFile();
    }

    public function testShouldReplacePlaceholderInDockerFile()
    {
        $path = __DIR__ . '/../Fixtures/';

        $dockerFilePath = $this->copyFile($path, 'docker-file_original.yml', 'docker-file.yml');
        $ip = new Ip(self::SUITABLE_IP);

        $this->dockerFile->replace($dockerFilePath, self::PLACEHOLDER, $ip);

        $this->assertFileEquals($path . 'docker-file_updated.yml', $dockerFilePath);
    }

    public function testShouldNOTReplacePlaceholderInDockerFileOnDryRun()
    {
        $path = __DIR__ . '/../Fixtures/';

        $dockerFilePath = $this->copyFile($path, 'docker-file_original.yml', 'docker-file.yml');
        $ip = new Ip(self::SUITABLE_IP);

        $this->dockerFile->replace($dockerFilePath, self::PLACEHOLDER, $ip, true);

        $this->assertFileNotEquals($path . 'docker-file_updated.yml', $dockerFilePath);
        $this->assertFileEquals($path . 'docker-file_original.yml', $dockerFilePath);
    }
}
