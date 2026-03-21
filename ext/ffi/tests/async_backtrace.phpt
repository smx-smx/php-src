--TEST--
FFI: debug_backtrace() inside callback should link to native call site
--EXTENSIONS--
ffi
--SKIPIF--
<?php
if (PHP_OS_FAMILY !== 'Linux') die('skip Linux only');
?>
--FILE--
<?php
$ffi = FFI::cdef("
    typedef void *(*pthread_callback)(void *);
    int pthread_create(unsigned long *thread, const void *attr, pthread_callback start_routine, void *arg);
    void usleep(unsigned int usec);
", "libc.so.6");

$callback = function($arg) {
    echo "In callback, trace contains:\n";
    $trace = debug_backtrace();
    foreach ($trace as $frame) {
        $func = $frame['function'] ?? 'unknown';
        if ($func === 'pthread_create' || $func === 'test') {
            echo "  - $func\n";
        }
    }
};

function test($ffi, $callback) {
    $thread = $ffi->new("unsigned long");
    $ffi->pthread_create(FFI::addr($thread), null, $callback, null);
    
    for ($i = 0; $i < 10; $i++) {
        $ffi->usleep(10000);
    }
}

test($ffi, $callback);
?>
--EXPECTF--
In callback, trace contains:
  - pthread_create
  - test
