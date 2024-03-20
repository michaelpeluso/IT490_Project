<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// get local service data from url
$service_type = isset($_GET['service_type']) ? $_GET['service_type'] : null;
$type_id = isset($_GET['type_id']) ? $_GET['type_id'] : null;

// build request
$request = array(
    'type' => "get_service_reviews",
    'service_type' => $service_type,
    'type_id' => $type_id
);

// connect to rabbitMQ
$response = "";
try {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);

} 
catch (Exception $e) {
    die("Error: " . $e->getMessage());
}    

// validate response
if ($response == false) {
    die("Failed to receive response from RabbitMQ server.");
}

// confirm ok response
if ($response['status'] !== "ok") {
	die("Could not connect to server.");
}

// return json
echo json_encode($response);
?>
