<?php

// dependecies
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

// get local service data from url
$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;
$service_type = isset($_GET['service_type']) ? $_GET['service_type'] : null;

// eventually we should change this to use ENVIORNMENT variables instead
$request = array(
    'type' => "get_service_reviews",
    'service_id' => $service_id,
    'service_type' => $service_type
);

// connect to rabbitMQ
try {
    $client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    
    if ($response !== false) {
        var_dump($response);
    } else {
        echo "Failed to receive response from RabbitMQ server.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// validate response
if ($response['status'] !== "ok") {
	die("Could not connect to server.");
}


// parse as json
//$json_response = json_encode($response);
//header('Content-Type: application/json');

// return data
//echo $json_data;

?>
