<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// get local service data from url
$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;

// eventually we should change this to use ENVIORNMENT variables instead
$request = array(
    'type' => "get_service_reviews",
    'service_id' => $service_id    
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
