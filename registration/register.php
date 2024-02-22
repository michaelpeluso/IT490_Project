#!/usr/bin/php
<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// Input Handling and Basic Sanitization
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password']; 
$first_name = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
$last_name  = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);

// Hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
//encrypting and encoding
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";
$SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

$encrypted_user = sodium_crypto_secretbox( $username, $nonce, $SODIUM_KEY);
$encrypted_email = sodium_crypto_secretbox( $email, $nonce, $SODIUM_KEY);

$encode_user  = base64_encode($nonce . $encrypted_user );
$encode_email = base64_encode($nonce . $encrypted_email);

// eventually we should change this to use ENVIORNMENT variables instead
$request = array(
    'type' => "register",
    'username' => $encode_user,
    'email' => $encode_email,
    'password' => $hashed_password,
    'first_name' => $first_name,
    'last_name' => $last_name,
);

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

echo "Server response: \n";
print_r($response);
?>
