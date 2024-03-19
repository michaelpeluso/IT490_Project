<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// get local service data
session_start();
$auth_key = $_SESSION['key'];

$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;

$review_rating = $_POST['rating']; // serialized from form
$review_body = $_POST['reviewText'];

$review_date = date("m-d-Y");

// pack data for request
$request = array(
    'type' => "post_review",
    'auth_key' => $auth_key,
    'user_id' => $user_id,
    'service_id' => $service_id,
    'review_rating' => $review_rating,
    'review_body' => $review_body,
    'review_date' => $review_date
);

// connect to rabbitMQ
$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

// log response
echo "Server response: \n";
print_r($response);

// validate response
if ($response['status'] !== "ok") {
	die("Could not connect to server.");
}

// parse as json
$json_response = json_encode($response);
header('Content-Type: application/json');

// return data
echo $json_data

?>
