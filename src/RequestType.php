<?php

namespace Kicken\PowerRequest;

enum RequestType {
    case PowerRequestDisplayRequired;
    case PowerRequestSystemRequired;
    case PowerRequestAwayModeRequired;
    case PowerRequestExecutionRequired;
}
