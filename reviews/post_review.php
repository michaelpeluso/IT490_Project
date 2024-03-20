<?php

// start session if needed
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// get local service data

$auth_key = isset($_SESSION['key']) ? $_SESSION['key'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$service_type = isset($_GET['service_type']) ? $_GET['service_type'] : null;
$type_id = isset($_GET['type_id']) ? $_GET['type_id'] : null;
$review_rating = isset($_GET['review_rating']) ? $_GET['review_rating'] : null;
$review_body = isset($_GET['review_body']) ? $_GET['review_body'] : null;

// pack data for request
$request = array(
    'type' => "post_review",
    'auth_key' => $auth_key,
    'user_id' => $user_id,
    'service_type' => $service_type,
    'type_id' => $type_id,
    'review_rating' => $review_rating,
    'review_body' => $review_body
);


$request = array(
    'type' => "post_review",
    'auth_key' => $auth_key,
    'user_id' => "1",
    'service_type' => "h",
    'type_id' => "1",
    'review_rating' => "5",
    'review_body' => "This is the review body"
);



// connect to rabbitMQ
try {
	
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    
    if ($response == false) {
        echo "Failed to receive response from RabbitMQ server." . PHP_EOL;
    }
}
catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// validate response
if (!isset($response['status'])) {
	die("Could not connect to server.");
}

// parse as json
echo json_encode($response);

?>
