--TEST--
mb_http_input()
--SKIPIF--
<?php
extension_loaded('mbstring') or die('skip mbstring not available');
?>
--POST--
a=日本語0123456789日本語カタカナひらがな
--GET--
b=日本語0123456789日本語カタカナひらがな
--INI--
mbstring.encoding_translation=1
input_encoding=latin1
--FILE--
<?php

echo $_POST['a']."\n";
echo $_GET['b']."\n";

// Get encoding
var_dump(mb_http_input('P'));
var_dump(mb_http_input('G'));

?>
--EXPECT--
??端????存狸0123456789??端????存狸促束促多促束促??造??造辿造測造??
??端????存狸0123456789??端????存狸促束促多促束促??造??造辿造測造??
string(10) "ISO-8859-1"
string(10) "ISO-8859-1"
