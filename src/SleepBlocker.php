<?php

namespace Kicken\SleepBlocker;

use Kicken\SleepBlocker\Implementation\Blocker;
use Kicken\SleepBlocker\Implementation\NullBlocker;
use Kicken\SleepBlocker\Implementation\WindowsBlocker;

abstract class SleepBlocker {
    public static function create() : Blocker{
        return match (PHP_OS_FAMILY) {
            'Windows' => new WindowsBlocker(),
            default => new NullBlocker(),
        };
    }
}
