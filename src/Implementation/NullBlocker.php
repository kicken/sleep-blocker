<?php

namespace Kicken\SleepBlocker\Implementation;

class NullBlocker implements Blocker {
    public function preventSleep(string $reason) : void{
    }

    public function allowSleep() : void{
    }

    public function isPreventingSleep() : bool{
        return false;
    }
}
