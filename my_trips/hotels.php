<?php
//DEPENDENCIES
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$string = file_get_contents('php://input');
$data = json_decode($string);

//echo "hotels data: ";
//var_dump($data);

if ($data === null ){
	echo "error parsing json";
}
else{

echo "sending to hotels api";
// make connection 
$client = new rabbitMQClient("../testRabbitMQ.ini","LiveDataServer");
// request
	$request = array(
	'type' => "hotels",
	"data" => $string, 
	'message' => "testing Hotels API",
	);
	// resposnse
	$response = $client->send_request($request);
	var_dump($response);
	
}

?>
