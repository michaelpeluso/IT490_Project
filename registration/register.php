<?php
// Input Handling and Basic Sanitization
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = $_POST['password']; 
$first_name = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
$last_name  = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
$creation_time = date('Y-m-d H:i:s');
// Password Hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// CHANGE TO RABBITMQ PATH
require_once('path/to/amqp.inc');
// eventually we should change this to use ENVIORNMENT variables instead
$conn = new AMQPConnection('rabbit_host', 'rabbit_port', 'rabbit_user', 'rabbit_password');
$channel = $conn->channel();

$messageBody = json_encode([
    'username' => $username,
    'email'    => $email,
    'password' => $hashed_password, 
    'first_name' => $first_name,
    'last_name' => $last_name,
    'creation_time' => $creation_time // timestamp

]);

$exchange_name = 'registration_data';
$queue_name = 'registration_queue';
$channel->exchange_declare($exchange_name, false, true, false); 
$channel->queue_declare($queue_name, false, true, false, false);
$channel->queue_bind($queue_name, $exchange_name);

// Send Message
$message = new AMQPMessage($messageBody);
$channel->basic_publish($message, $exchange_name);

$channel->close();
$conn->close();

// Return
echo "Registration data sent!"; 
?>
