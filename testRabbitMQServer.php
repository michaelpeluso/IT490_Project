#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('condb.php');

global $SODIUM_KEY_hex;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

function doLogin($username,$password, $email)
{
    //converting secret key from hex to binary
    global $SODIUM_KEY_hex;
    $SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
    
    //decoding encrypted messages
    $decode = base64_decode($username, false);
    $nonce = mb_substr($decode, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    echo "".base64_encode($nonce);
    $encrypted_user = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $username = sodium_crypto_secretbox_open($encrypted_user, $nonce, $SODIUM_KEY );
    
    $decode = base64_decode($password, false);
    $encrypted_pass = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $password = sodium_crypto_secretbox_open($encrypted_pass, $nonce, $SODIUM_KEY );
    
    $decode = base64_decode($email, false);
    $encrypted_email = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $email = sodium_crypto_secretbox_open($encrypted_email, $nonce, $SODIUM_KEY );
    
    echo $plain_text;

    // connecting to database
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
    
    $query = "select id, username, email, password from user where (username = '".$username."' or email = '".$email."');";
	
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
    
    $row = $response -> fetch_assoc();
    if(!password_verify($password, $row['password'])){
    	
    	echo "wrong password";
    	return "wrong password";
    }
    else {
    	echo "password is correct!";
    }
    $id = $row['id'];
    //update users authkey to its current value.
    $query = "update user set authkey = SHA2(CONCAT(username,password, last_update),256) where id = '".$id."';";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    
    //get users auth key and return it
    $query = "select authkey from user where id='".$id."';";

    $response= $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    exit(0);
    }
    
    $row = $response -> fetch_assoc();
    
    $mydb->close();
    echo "Logged in, Authkey:".$row['authkey']."\n";
    return "Logged in, Authkey:".$row['authkey']."\n";
    
}

function doRegister($username,$password,$email,$firstName,$lastName)
{

    //converting secret key from hex to binary
    global $SODIUM_KEY_hex;
    $SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
    
    //decoding encrypted messages
    $decode = base64_decode($username, false);
    $nonce = mb_substr($decode, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    
    $encrypted_user = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $username = sodium_crypto_secretbox_open($encrypted_user, $nonce, $SODIUM_KEY );
    
    $decode = base64_decode($email, false);
    $encrypted_email = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $email = sodium_crypto_secretbox_open($encrypted_email, $nonce, $SODIUM_KEY );
    
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

