--TEST--
Bug #79177 (FFI doesn't handle well PHP exceptions within callback)
--EXTENSIONS--
ffi
zend_test
--FILE--
<?php
$header = <<<HEADER
extern int *(*bug79177_cb)(void);
void bug79177(void);
HEADER;

$ffi = FFI::cdef($header);
$ffi->bug79177_cb = function() {
    throw new \RuntimeException('Not allowed');
};
try {
    $ffi->bug79177();
} catch (\Throwable $exception) {
    echo "Caught: " . $exception->getMessage() . "\n";
}
echo "done\n";
?>
--EXPECT--
Caught: Not allowed
done
