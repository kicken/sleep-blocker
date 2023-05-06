<?php

namespace Kicken\PowerRequest;

use FFI;
use FFI\CData;
use RuntimeException;

class Request {
    private FFI $ffi;
    private CData $request;
    private RequestType $type;
    private bool $isSet = false;

    private const POWER_REQUEST_CONTEXT_VERSION = 0;
    private const POWER_REQUEST_CONTEXT_SIMPLE_STRING = 1;

    public function __construct(string $reason, bool $autoSet = true, RequestType $type = RequestType::PowerRequestSystemRequired){
        if (!extension_loaded('ffi')){
            throw new RuntimeException('FFI extension is required.');
        }

        $this->createFFIObject();
        $this->createRequest($reason);
        $this->type = $type;
        if ($autoSet){
            $this->set();
        }
    }

    public function __destruct(){
        $this->clearRequest(false);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->ffi->CloseHandle($this->request);
    }

    public function set() : void{
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $success = $this->ffi->PowerSetRequest($this->request, match ($this->type) {
            RequestType::PowerRequestDisplayRequired => $this->ffi->PowerRequestDisplayRequired,
            RequestType::PowerRequestAwayModeRequired => $this->ffi->PowerRequestAwayModeRequired,
            RequestType::PowerRequestExecutionRequired => $this->ffi->PowerRequestExecutionRequired,
            default => $this->ffi->PowerRequestSystemRequired
        });
        if (!$success){
            throw new RuntimeException('Unable to set power request.');
        }

        $this->isSet = true;
    }

    public function clear() : void{
        $this->clearRequest();
    }

    private function clearRequest(bool $throw = true) : void{
        if (!$this->isSet){
            return;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $success = $this->ffi->PowerClearRequest($this->request, $this->ffi->PowerRequestSystemRequired);
        if (!$success && $throw){
            throw new RuntimeException('Unable to clear request.');
        }
        $this->isSet = false;
    }

    private function createFFIObject() : void{
        $this->ffi = FFI::cdef('
typedef unsigned short wchar_t;
typedef unsigned long ULONG;
typedef unsigned long DWORD;
typedef int WINBOOL;
typedef wchar_t *LPWSTR;
typedef struct { int unused; } *HMODULE;
typedef void *HANDLE;
typedef struct _REASON_CONTEXT {
    ULONG Version;
    DWORD Flags;
    union {
        struct {
            HMODULE LocalizedReasonModule;
            ULONG LocalizedReasonId;
            ULONG ReasonStringCount;
            LPWSTR *ReasonStrings;
        } Detailed;
        LPWSTR SimpleReasonString;
    } Reason;
} REASON_CONTEXT, *PREASON_CONTEXT;

typedef enum _POWER_REQUEST_TYPE {
    PowerRequestDisplayRequired,
    PowerRequestSystemRequired,
    PowerRequestAwayModeRequired,
    PowerRequestExecutionRequired
} POWER_REQUEST_TYPE;

HANDLE PowerCreateRequest(PREASON_CONTEXT Context);
WINBOOL PowerSetRequest(HANDLE PowerRequest, POWER_REQUEST_TYPE RequestType);
WINBOOL PowerClearRequest(HANDLE PowerRequest, POWER_REQUEST_TYPE RequestType);
WINBOOL CloseHandle(HANDLE hObject);
', 'kernel32.dll');
    }

    /** @noinspection PhpUndefinedFieldInspection */
    private function createRequest(string $reason) : void{
        $context = $this->ffi->new('REASON_CONTEXT', false);
        if (!$context){
            throw new RuntimeException('Failed to create context');
        }

        $context->Version = self::POWER_REQUEST_CONTEXT_VERSION;
        $context->Flags = self::POWER_REQUEST_CONTEXT_SIMPLE_STRING;
        $context->Reason->SimpleReasonString = $this->stringToWSTR($reason);

        /** @noinspection PhpUndefinedMethodInspection */
        $request = $this->ffi->PowerCreateRequest(FFI::addr($context));
        if (!$request){
            throw new RuntimeException('Failed to create power request.');
        }

        $this->request = $request;
    }

    private function stringToWSTR(string $string) : FFI\CData{
        $length = strlen($string) + 1;
        $arrayType = FFI::arrayType($this->ffi->type('wchar_t'), [$length]);
        $arrayData = $this->ffi->new($arrayType, false);
        if (!$arrayData){
            throw new RuntimeException('Unable to allocate arrayData string.');
        }

        for ($i = 0; $i < $length - 1; $i++){
            $arrayData[$i] = ord($string[$i]);
        }
        $arrayData[$length - 1] = 0;

        return $arrayData;
    }
}
