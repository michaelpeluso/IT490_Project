#!/usr/bin/php
<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// retrieve user values
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);

// hash
$hashedUsername = password_hash($username, PASSWORD_DEFAULT);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// make connection
$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");


// request
$request = array(
	'type' => "login",
	'username' => $hashedUsername,
	'password' => $hashedPassword,
	'email' => $email,
	'message' => "login attepmt made by " . $email
);

// resposnse
$response = $client->send_request($request);
//$response = $client->publish($request);

echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;

