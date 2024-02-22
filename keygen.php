#!/usr/bin/php
<?php

$key = sodium_crypto_secretbox_keygen();
var_dump(sodium_bin2hex($key));
//$key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
//echo $key;

?>
