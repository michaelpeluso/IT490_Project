#!/usr/bin/php
<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

global $SODIUM_KEY;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

// retrieve user values

$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);


// make connection
$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");

//encrypt and encode
$SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
echo base64_encode($nonce);

$encrypted_email = sodium_crypto_secretbox( $email, $nonce, $SODIUM_KEY);
$encrypted_pass = sodium_crypto_secretbox( $password, $nonce, $SODIUM_KEY);

$encode_email = base64_encode($nonce . $encrypted_email);
$encode_pass  = base64_encode($nonce . $encrypted_pass );

echo ("before");
// request
$request = array(
	'type' => "login",
	'password' => $encode_pass,
	'email' => $encode_email,
	'message' => "login attepmt made by "
);


// resposnse
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

echo (var_dump($response));
echo ($response['status']);
if ($response['status'] === "ok"){
	session_start();
	$_SESSION["key"] = $response['key'];
	$_SESSION["first_name"] = $response['first_name'];
	$_SESSION["last_name"] = $response['last_name'];
	$_SESSION["email"] = $response['email'];
	header("Location: http://100.35.46.200/IT490_Project/registered/");
}
?>


