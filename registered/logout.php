#!/usr/bin/php
<?php
// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

session_start();

$request = array(
	'type' => "logout",
	'key' => $_SESSION["key"]
);


$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

session_unset();
session_destroy();

header("location:../");
?>
