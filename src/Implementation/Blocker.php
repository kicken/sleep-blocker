<?php

namespace Kicken\SleepBlocker\Implementation;

interface Blocker {
    public function __construct(string $defaultReason);

    public function preventSleep(string $reason = null) : void;

    public function allowSleep() : void;

    public function isPreventingSleep() : bool;
}
