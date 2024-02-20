<?php

// require
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require_once('path/to/amqp.inc');

// retrieve data
$username = $_POST["username"];
$password = $_POST["password"];
$email = $_POST["email"];

// hash
$hashedUsername = password_hash($username, PASSWORD_DEFAULT);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// connect to rabbitMQ
$conn = new AMQPConnection('rabbit_host', 'rabbit_port', 'rabbit_user', 'rabbit_password');
$channel = $conn->channel();

$exchange_name = 'login_data';
$queue_name = 'login_queue';
$channel->exchange_declare($exchange_name, false, true, false); 
$channel->queue_declare($queue_name, false, true, false, false);
$channel->queue_bind($queue_name, $exchange_name);

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

// request
$request = array(
	'username' => $hasedUsername,
	'password' => $hasedPassword,
	'email' => $email
);

$response = new AMQPMessage($request);
$channel->basic_publish($response, $exchange_name);

$channel->close();
$conn->close();

// Handle response
$payload = json_encode($response);
echo "Client received response:\n";
var_dump($response);
echo "\n\n";
	echo "Registration completed.";

?>

