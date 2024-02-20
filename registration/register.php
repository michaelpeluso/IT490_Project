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

'''
$username = "username";
$email    = "e@mail.com";
$password = "password"; 
$first_name = "John";
$last_name  = "Doe";
'''

// Hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$hashed_user = password_hash($username, PASSWORD_DEFAULT);

// eventually we should change this to use ENVIORNMENT variables instead
$request = array(
    'type' => "register",
    'username' => $username,
    'email' => $email,
    'password' => $hashed_password,
    'first_name' => $first_name,
    'last_name' => $last_name,
);

$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");
$response = $client->send_request($request);

echo "Server response: \n";
print_r($response);
?>
