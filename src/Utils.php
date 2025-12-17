<?php

namespace Nacosvel\Helper;

use Nacosvel\Helper\Concerns\ArrayTrait;
use Nacosvel\Helper\Concerns\StringTrait;

final class Utils
{
    use ArrayTrait, StringTrait;

    /**
     * Get hashCode for give string
     *
     * @param string $data
     *
     * @return int
     */
    public static function hashCode(string $data): int
    {
        return hexdec(hash('crc32', $data));
    }
}
