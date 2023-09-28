<?php

namespace Kicken\SleepBlocker\Implementation;

interface Blocker {
    public function preventSleep(string $reason) : void;

    public function allowSleep() : void;

    public function isPreventingSleep() : bool;
}
