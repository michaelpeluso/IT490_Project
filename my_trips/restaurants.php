#!/usr/bin/php
<?php
//DEPENDENCIES
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$string = file_get_contents('php://input');
$data = json_decode($string);

//var_dump($data);

if ($data === null ){
	echo "error parsing json";
}
else{


// make connection 
$client = new rabbitMQClient("../testRabbitMQ.ini","LiveDataServer");
// request
	$request = array(
	'type' => "restaurants",
	"data" => $string, 
	'message' => "testing Restaurant API",
	);
	// resposnse
	$response = $client->send_request($request);
	var_dump($response);
	//header("Content-Type: application/json");
	//echo json_encode(array("latitude" => $_GET['latitude'], "longitude" => $_GET['longitude'], "radius"=>$_GET['radius']));
	
	
}

?>
