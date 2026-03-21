--TEST--
FFI thread-safe callback: Basic functionality
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
    int pthread_join(unsigned long thread, void **retval);
    void usleep(unsigned int usec);
", "libc.so.6");

$callback = function($arg) {
    echo "Called from thread!\n";
    return null;
};

$thread = $ffi->new("unsigned long");
if ($ffi->pthread_create(FFI::addr($thread), null, $callback, null) !== 0) {
    die("pthread_create failed\n");
}
echo "Thread created\n";

for ($i = 0; $i < 100; $i++) {
    $ffi->usleep(1000);
}

$ffi->pthread_join($thread->cdata, null);
echo "Thread joined\n";
?>
--EXPECT--
Thread created
Called from thread!
Thread joined
