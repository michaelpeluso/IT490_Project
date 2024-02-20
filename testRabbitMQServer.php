#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('condb.php');

function doLogin($username,$password, $email)
{
    // connecting to database
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
    
    $query = "select username, email, password from user where (username = '".$username."' or email = '".$email."') and password = '".$password."';";
	
    $response = $mydb->query($query);
    if ($mydb->errno != 0)
    {
	echo "failed to execute login query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    if($response -> num_rows == 0){
	echo "wrong credentials";
	return "wrong credentials";
    }
    
    
    
    //update users authkey to its current value.
    $query = "update user set authkey = SHA2(CONCAT(username,password, last_update),256) where (username = '".$username."' or email = '".$email."') and password = '".$password."';";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    
    //get users auth key and return it
    $query = "select authkey from user where (username = '".$username."' or email = '".$email."') and password = '".$password."';";

    $response= $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    
    $row = $response -> fetch_assoc();
    
    $mydb->close();
    echo "Logged in, Authkey:".$row['authkey'];
    return "Logged in, Authkey: ";
    
}

function doRegister($username,$password,$email,$firstName,$lastName)
{
    // connecting to database
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
    
    $query = "select username, email from user where username = '".$username."' or email = '".$email."';";
	
    $response = $mydb->query($query);
    if($response -> num_rows>=1){
	echo "username or email in use";
	return "username or email in use";
    }
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    
    //insert user to database
    
    $query = "insert into user (firstName, lastName, username, password, email) values ('".$firstName."', '".$lastName."', '".$username."', '".$password."', '".$email."' );";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }

    //update users auth to its initial value.
    $query = "update user set authkey = SHA2(CONCAT(username,password, last_update),256) where username = '".$username."' and password = '".$password."';";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    // lookup username in database return empty
    // register him
    $mydb->close();
    echo "Registered:".$email;
    return "Registered";
    
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
    case "login":
      return doLogin($request['username'],$request['password'], $request['email']);
    case "register":
    	return doRegister($request['username'], $request['password'], $request['email'], $request['first_name'], $request['last_name'] );
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");
echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
exit();
?>

