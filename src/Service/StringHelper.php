<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;

class StringHelper
{
    public function contains(string $haystack, string $needle): bool
    {
        Assertion::notEmpty($needle);

        if (empty($haystack)) {
            return false;
        }

        return strpos($haystack, $needle) !== false;
    }
}
