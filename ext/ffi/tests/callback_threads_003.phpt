--TEST--
FFI thread-safe callback: Concurrent execution
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

$callback = function($arg) use ($ffi) {
    $id_ptr = $ffi->cast("long*", $arg);
    $id = (int)$id_ptr[0];
    echo "Callback from thread $id\n";
    return null;
};

$num_threads = 5;
echo "$num_threads threads created. Waiting...\n";

$threads = [];
$args = [];
for ($i = 0; $i < $num_threads; $i++) {
    $thread = $ffi->new("unsigned long");
    $arg = $ffi->new("long", false);
    $arg->cdata = $i;
    $args[] = $arg;
    
    if ($ffi->pthread_create(FFI::addr($thread), null, $callback, FFI::addr($arg)) !== 0) {
        die("pthread_create failed at $i\n");
    }
    $threads[] = $thread;
}

for ($i = 0; $i < 200; $i++) {
    $ffi->usleep(1000);
}

foreach ($threads as $i => $thread) {
    $ffi->pthread_join($thread->cdata, null);
    echo "Thread $i joined\n";
}
?>
--EXPECTF--
5 threads created. Waiting...
%s
%s
%s
%s
%s
Thread 0 joined
Thread 1 joined
Thread 2 joined
Thread 3 joined
Thread 4 joined
