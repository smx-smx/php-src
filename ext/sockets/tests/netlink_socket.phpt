--TEST--
Test for socket_create() with netlink
--SKIPIF--
<?php
if (!extension_loaded('sockets')) {
    die('SKIP The sockets extension is not loaded.');
}
if (strtolower(PHP_OS) !== 'linux'){
    die('SKIP Netlink requires Linux');
}
--FILE--
<?php
$socket = socket_create(AF_NETLINK, SOCK_RAW, 0);
$s_bind = socket_bind($socket, null);
var_dump($socket);
var_dump($s_bind);
--EXPECT--
resource(4) of type (Socket)
bool(true)
