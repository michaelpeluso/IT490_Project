<?php
// Database connection settings
//send email!
//DEPENDENCIES
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');


session_start();
$email = $_SESSION["email"];

echo $email;

$string = file_get_contents('php://input');
$data = json_decode($string);


if ($data === null ){
	echo "error parsing json";
}
else{

//echo "sending to Emails api ".$data->title;
// make connection 
$client = new rabbitMQClient("../testRabbitMQ.ini","LiveDataServer");
// request
	$request = array(
	'type' => "emails",
	"title" => $data->title, 
	"description" =>$data -> description,
	"date" => $data -> date,
	"email" => $email,
	'message' => "testing Email API",
	);
	echo "this is the request: ".$request;
	// resposnse
	$response = $client->send_request($request);
	//var_dump($response);
	
}





?>
