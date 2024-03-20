#!/usr/bin/php
<?php
//DEPENDENCIES
require_once('./path.inc');
require_once('./get_host_info.inc');
require_once('./rabbitMQLib.inc');


// make connection
$client = new rabbitMQClient("./testRabbitMQ.ini","LiveServer");

//encrypt and encode

// request
$request = array(
	'type' => "hotel",
	'message' => "testing queue and "
);


// resposnse
$response = $client->send_request($request);
//$response = $client->publish($request);
echo ("after");
echo "client received response: ".PHP_EOL;
print_r($response);
echo "\n\n";

echo $argv[0]." END".PHP_EOL;
echo (var_dump($response));
echo ($response['status']);

?>

