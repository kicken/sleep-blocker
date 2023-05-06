# PHP Power Requests
Create a power request to prevent a computer from going to sleep.  

## Requirements
 * Windows
 * [FFI Extension](https://www.php.net/manual/en/book.ffi.php)

## Example
Construct an instance of Kicken\PowerRequest\Request() with a description of the reason for your request and the type of request you wish to create.  The request will be created as part of the object construction and released when the object goes out of scope.

    $request = new Kicken\PowerRequest\Request("Script is working");

You may manually set and release request by using the `set` and `clear` methods.

    $request = new Kicken\PowerRequest\Request('Connected to client.', false);
    $server = stream_socket_server('tcp://0.0.0.0:9');
    while (true){
        $client = stream_socket_accept($server, null);
        if ($client){
            $request->set();
            do {
                $data = fread($client, 1024);
                if (!$data){
                    fclose($client);
                    $client = null;
                }
            } while ($client);
            $request->clear();
        }
    }

## Request types
The following request types are supported:

* PowerRequestDisplayRequired
* PowerRequestSystemRequired
* PowerRequestAwayModeRequired
* PowerRequestExecutionRequired

By default, the `PowerRequestSystemRequired` type is used.  For details about the request types, refer to the pMicrosoft documentation](https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-powersetrequest).
