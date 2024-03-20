#!/usr/bin/php
<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('condb.php');

global $SODIUM_KEY_hex;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

function doHotels($data)
{

	$mydb = new mysqli('127.0.0.1','register','pwd','IT490');
    	if ($mydb->errno != 0)	
    	{
		echo "failed to connect to database: ". $mydb->error . PHP_EOL;
		return array("error"=>"server error",
		"status"=>"error");
		exit(0);
    	}

    	echo "successfully connected to database".PHP_EOL;

	$parsed = json_decode($data);
	//var_dump($parsed);
	var_dump($parsed->data);
	$hotelOffers = $parsed->data;
	
	foreach ($hotelOffers as $hotelOffer){
		$hotel = $hotelOffer->hotel;
		$offer = $hotelOffer->offers[0];
		
		echo $hotel -> name."\n";
		echo $hotel -> cityCode."\n";
		echo $hotel -> hotelId."\n";
		
		
		$query = "select * from hotels where hotelID = '".$hotel -> hotelId."';";
    		$response = $mydb->query($query);
	    	if ($mydb->errno != 0)
	    	{
			echo "failed to execute login query:".PHP_EOL;
	    		echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	    		return array(
			'status'=> "error",
			'error'=> "Server Error");
	    		exit(0);
	    	}
    	
    	  	if($response -> num_rows == 0){
		    	$query = 'insert into hotels (name, cityCode, hotelID) values ( "'.$hotel -> name.'", "'.$hotel -> cityCode.'", "'.$hotel -> hotelId.'" );';
			//echo $query;
			echo "\ninserted to database: ".$hotel -> hotelId;
		    	$mydb->query($query);
		    	if ($mydb->errno != 0)
		    	{
				echo "failed to execute login query:".PHP_EOL;
		    		echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
		    		return array(
				'status'=> "error",
				'error'=> "Server Error");
		    		exit(0);
		    	}
    	
    		}
		//var_dump ($hotelOffer->hotel->name);
	
	
	}
	
	$mydb-> close();

	return "success";
}

function doFlights($data)
{
    return "success";
}

function doRestaurants($data)
{

$mydb = new mysqli('127.0.0.1','register','pwd','IT490');
    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	return array("error"=>"server error",
	"status"=>"error");
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
   //insert a new restaurant to the database
    $parsed = json_decode($data);
    //var_dump($parsed);
    foreach($parsed as $restaurant){
    
    	//var_dump($restaurant);
    	/*echo "\n\n".$restaurant-> name . "\n";
    	echo $restaurant-> rating. "\n";
    	echo $restaurant-> vicinity. "\n";
    	echo $restaurant-> place_id."\n";*/
    	
    	$query = "select * from restaurants where placeID = '".$restaurant-> place_id."';";
    	$response = $mydb->query($query);
    	if ($mydb->errno != 0)
    	{
		echo "failed to execute login query:".PHP_EOL;
    		echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    		return array(
		'status'=> "error",
		'error'=> "Server Error");
    		exit(0);
    	}
    	
    	if($response -> num_rows == 0){
	    	$query = 'insert into restaurants (name, rating, address, placeID) values ( "'.$restaurant-> name.'", "'.$restaurant-> rating.'", "'.$restaurant-> vicinity.'", "'.$restaurant-> place_id.'" );';
		//echo $query;
		echo "\ninserted to database: ".$restaurant-> place_id;
	    	$mydb->query($query);
	    	if ($mydb->errno != 0)
	    	{
			echo "failed to execute login query:".PHP_EOL;
	    		echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	    		return array(
			'status'=> "error",
			'error'=> "Server Error");
	    		exit(0);
	    	}
    	
    	}
    }
    
    $mydb -> close();
   return array ("status" => "ok",
   "message" => "all restaurants were added");
}


function doEmails($title, $description, $date , $email)
{
	

	if($description == ""){
		return "no trips";
	}
	
	    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }
    echo "successfully connected to database".PHP_EOL;
    
    $query = "insert into email (subject, message, subDate, reciverAdd) values('".$title."','".$description."','".$date."','".$email."');";
    
    // get current date time
    $mydb->query($query);
    if ($mydb->errno != 0)
    {
	echo "failed to execute login query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    	return array(
	'status'=> "error",
	'error'=> "Server error");
    exit(0);
    }
    
    return "success";
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  //var_dump($request);
  if(!isset($request['type']))
  {
    return array("returnCode" => '1', 'message' => "ERROR: unsupported message type", 'status'=>'error');
  }
  echo($request['type']."\n");
  echo($request['message']."\n");
  switch ($request['type'])
  {
    case "emails":
    	return doEmails($request["title"], $request["description"], $request["date"], $request["email"] );
    case "hotels":
      return doHotels($request["data"]);
    case "flights":
    	return doFlights($request["data"]);
    case "restaurants":
    	return doRestaurants($request["data"]);
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

