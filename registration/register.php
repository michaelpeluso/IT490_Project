#!/usr/bin/php
<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

global $SODIUM_KEY;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

// retrieve user values
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);


// make connection
$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");

//encrypt and encode
$SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
echo base64_encode($nonce);

$encrypted_user = sodium_crypto_secretbox( $username, $nonce, $SODIUM_KEY);
$encrypted_email = sodium_crypto_secretbox( $email, $nonce, $SODIUM_KEY);
$encrypted_pass = sodium_crypto_secretbox( $password, $nonce, $SODIUM_KEY);

$encode_user  = base64_encode($nonce . $encrypted_user );
$encode_email = base64_encode($nonce . $encrypted_email);
$encode_pass  = base64_encode($nonce . $encrypted_pass );


// request
$request = array(
	'type' => "login",
	'username' => $encode_user,
	'password' => $encode_pass,
	'email' => $encode_email,
	'message' => "login attepmt made by " . $email
);

// resposnse
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;