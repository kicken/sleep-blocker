# Sleep Blocker

Allow PHP scripts to prevent a system from going to sleep.

## Example

Obtain a blocker by calling SleepBlocker::create. This will return a blocker implementation appropriate for the current system.

    $blocker = Kicken\SleepBlocker\SleepBlocker::create();

Use the blocker methods preventSleep and allowSleep to disable or enable the system's ability to enter sleep mode.

    $server = stream_socket_server('tcp://0.0.0.0:9');
    while (true){
        $client = stream_socket_accept($server, null);
        if ($client){
            $blocker->preventSleep();
            do {
                $data = fread($client, 1024);
                if (!$data){
                    fclose($client);
                    $client = null;
                }
            } while ($client);
            $blocker->sllowSleep();
        }
    }

## Known issues

Currently only Microsoft Windows systems are supported.
