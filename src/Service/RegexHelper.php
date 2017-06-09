<?php

namespace MF\DockerIp\Service;

use Assert\Assertion;

class RegexHelper
{
    public function parse(string $string, string $pattern): ?string
    {
        Assertion::notEmpty($pattern);

        if (empty($string)) {
            return null;
        }

        preg_match($pattern, $string, $matches);

        return array_pop($matches);
    }

    public function contains(string $string, string $pattern): bool
    {
        Assertion::notEmpty($pattern);

        if (empty($string)) {
            return false;
        }

        return preg_match($pattern, $string);
    }
}
