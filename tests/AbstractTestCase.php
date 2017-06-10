<?php

namespace MF\DockerIp\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function copyFile(string $path, string $source, string $target): string
    {
        $hostsSourcePath = $path . $source;
        $targetPath = $path . $target;

        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        copy($hostsSourcePath, $targetPath);

        return $targetPath;
    }

    public function tearDown()
    {
        m::close();
    }
}
