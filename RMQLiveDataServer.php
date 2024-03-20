#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('condb.php');

global $SODIUM_KEY_hex;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

function doHotels($key)
{

return "success";
}

function doFlights($data)
{
    return "success";
}

function doRestaurants($password)
{
   //insert a new restaurant to the database
    
   return "success";
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return array("returnCode" => '1', 'message' => "ERROR: unsupported message type", 'status'=>'error');
  }
  switch ($request['type'])
  {
    case "hotels":
      return doHotels($request);
    case "flights":
    	return doFlights($request["data"]);
    case "restaurants":
    	return doRestaurants($request);
    default:
    	return array("returnCode" => '1', 'message' => "ERROR: unsupported message type", 'status'=>'error');
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","LiveDataServer");
echo "LiveDataRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
exit();
?>

