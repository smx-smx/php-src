--TEST--
FFI: debug_backtrace() inside callback
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

    typedef int (*qsort_compar)(const void *, const void *);
    void qsort(void *base, size_t nmemb, size_t size, qsort_compar compar);
", "libc.so.6");

echo "--- Synchronous (qsort) ---\n";
$array = $ffi->new("int[2]");
$array[0] = 2;
$array[1] = 1;
$qsort_callback = function($a, $b) {
    echo "In qsort callback:\n";
    debug_print_backtrace();
    return 0;
};
$ffi->qsort($array, 2, 4, $qsort_callback);

echo "\n--- Asynchronous (pthread_create) ---\n";
$pthread_callback = function($arg) {
    echo "In pthread callback:\n";
    debug_print_backtrace();
    return null;
};

$thread = $ffi->new("unsigned long");
if ($ffi->pthread_create(FFI::addr($thread), null, $pthread_callback, null) !== 0) {
    die("pthread_create failed\n");
}

for ($i = 0; $i < 1000; $i++) {
    $ffi->usleep(100);
}

$ffi->pthread_join($thread->cdata, null);
?>
--EXPECTF--
--- Synchronous (qsort) ---
In qsort callback:
#0 %sbacktraces.php(21): {closure:%s:%d}(Object(FFI\CData:void*), Object(FFI\CData:void*))
#1 %sbacktraces.php(21): FFI->qsort(Object(FFI\CData:int32_t[2]), 2, 4, Object(Closure))

--- Asynchronous (pthread_create) ---
In pthread callback:
#0 %sbacktraces.php(31): {closure:%s:%d}(NULL)
#1 %sbacktraces.php(31): FFI->pthread_create(Object(FFI\CData:%s), NULL, Object(Closure), NULL)
