#!/usr/bin/php
<?php
//DEPENDENCIES
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$string = file_get_contents('php://input');
$data = json_decode($string);

//var_dump($data);

//$response = "Received Data: ". $data;
echo "test";
//echo $response;

if ($data === null ){
	echo "error parsing json";
}
else{

var_dump($data->data);

// make connection 
$client = new rabbitMQClient("../testRabbitMQ.ini","LiveDataServer");
// request
	$request = array(
	'type' => "flights",
	"data" => $string, 
	'message' => "testing flight API",
	);
	// resposnse
	$response = $client->send_request($request);
	//header("Content-Type: application/json");
	//echo json_encode(array("latitude" => $_GET['latitude'], "longitude" => $_GET['longitude'], "radius"=>$_GET['radius']));
	
	
}

?>
