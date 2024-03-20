#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
include('condb.php');

global $SODIUM_KEY_hex;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

//
// DO VALIDATE
//
function doValidate($key){
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');
    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	return ("server error");
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
    
    $query = "select * from user where authkey = '".$key."';";
	
    $response = $mydb->query($query);
    if ($mydb->errno != 0)
    {
	echo "failed to execute login query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    	return ("server error");
    exit(0);
    }
    if($response -> num_rows == 0){
	echo "wrong credentials";
	return "b1b3b674b41941ada10db032ba86bed1a7cb73101560ac8e771b9990bb42bfbc";
    }
    
    return ("valid key");

}

//
// DO LOGIN
//
function doLogin($password, $email)
{
    //converting secret key from hex to binary
    global $SODIUM_KEY_hex;
    $SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
    
    //decoding encrypted messages
    $decode = base64_decode($email, false);
    $nonce = mb_substr($decode, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    //echo "".base64_encode($nonce);
    $encrypted_email = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $email = sodium_crypto_secretbox_open($encrypted_email, $nonce, $SODIUM_KEY );
    
    
    $decode = base64_decode($password, false);
    $encrypted_pass = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $password = sodium_crypto_secretbox_open($encrypted_pass, $nonce, $SODIUM_KEY );
    
    // connecting to database
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    
    // check if username or email already exists if it does send back error
    
    $query = "select id, email, password from user where email = '".$email."';";
    
    $response = $mydb->query($query);
    if ($mydb->errno != 0)
    {
	echo "failed to execute login query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    	return array(
	'status'=> "error",
	'error'=> "Server error");
    exit(0);
    }
    if($response -> num_rows == 0){
	echo "wrong credentials";
	return array(
	'status'=> "error",
	'error'=> "Wrong credentials");
    }
    
    $row = $response -> fetch_assoc();
    if(!password_verify($password, $row['password'])){
    	
    	echo "wrong password";
    	return array(
	'status'=> "error",
	'error'=> "Wrong credentials");
    }
    else {
    	echo "password is correct!";
    }
    
    $id = $row['id'];
    //update users authkey to its current value.
    $query = "update user set authkey = SHA2(CONCAT(email,password, last_update),256) where id = '".$id."';";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	return array(
	'status'=> "error",
	'error'=> "Server Error");
    exit(0);
    }
    
    //get users auth key and return it
    $query = "select authkey, firstName, lastName, email from user where id='".$id."';";

    $response= $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	return array(
	'status'=> "error",
	'error'=> "Server Error");
    exit(0);
    }
    
    $row = $response -> fetch_assoc();
    
    $mydb->close();
    echo "Logged in";
    
    $data = array(
    'status' => "ok",
    'email' => $row['email'],
    'first_name' => $row['firstName'],
    'last_name' => $row['lastName'],
    'user_id'=> $id,
    'key'=> $row['authkey'],
    );
    //echo (var_dump($data));
    return $data;
    
}

//
// DO REGISTER
//
function doRegister($password,$email,$firstName,$lastName)
{

    //converting secret key from hex to binary
    global $SODIUM_KEY_hex;
    $SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
    
    //decoding encrypted messages
    $decode = base64_decode($email, false);
    $nonce = mb_substr($decode, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
    $encrypted_email = mb_substr($decode , SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null,'8bit');
    $email = sodium_crypto_secretbox_open($encrypted_email, $nonce, $SODIUM_KEY );
    
    // connecting to database
    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	return array(
	'status'=> "error",
	'error'=> "Server Error");
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    // check if username or email already exists if it does send back error
    
    $query = "select email from user where email = '".$email."';";
	
    $response = $mydb->query($query);
    if($response -> num_rows>=1){
	echo "username or email in use";
	return array(
	'status'=> "error",
	'error'=> "username or email in use");
    }
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    	return array(
	'status'=> "error",
	'error'=> "Server Error");
    exit(0);
    }
    
    //insert user to database
    $query = "insert into user (firstName, lastName, username, password, email) values ('".$firstName."', '".$lastName."', '".$firstName."', '".$password."', '".$email."' );";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
    	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
    	return array(
	'status'=> "error",
	'error'=> "Server Error");
    exit(0);
    }

    //update users auth to its initial value.
    $query = "update user set authkey = SHA2(CONCAT(email,password, last_update),256) where email = '".$email."' and password = '".$password."';";

    $mydb->query($query);
	
    if ($mydb->errno != 0)
    {
	echo "failed to execute request query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	return array(
	'status'=> "error",
	'error'=> "Server Error");
    exit(0);
    }
    // lookup username in database return empty
    // register him
    $mydb->close();
    echo "Registered:".$email;
    return array("status"=>"ok", "message"=>"User Registered");
    
}

//
// POST REVIEW
//

function postReview($auth_key, $user_id, $service_type, $type_id, $review_rating, $review_body) {
	
	// error handling: validate all parameters
	$params = array("user_id", "auth_key", "service_type", "type_id", "review_rating", "review_body");
	foreach($params as $param) {
		if (!isset($$param)) {
		    return createError("No ". $$param ." provided");
		}
	}
	
	// validate accesskey
	$validation = doValidate($auth_key);
	if ($validation != "valid key") {
		return $validation;
	}

    // connect to database
	$mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work
    
    if ($mydb->errno != 0) {
    	// error hgandling: check for valid connection
		return createError("Failed to establish database connection");
    }
    echo "Successfully connected to database".PHP_EOL;
    
    // query the database
    $query_user = "select * from reviews where authkey = '".$auth_key."';";
    $response_user = $mydb->query($query_user);
    
    // error handling: check if auth key record exists
    if ($response_user->num_rows == 0) {
		return createError("No user with given auth key");
    }
    
    // insert user reviews
    $query_user = "insert into reviews (userID, type, typeID, review_raing, review_body) values ('".$user_id."', '".$service_type."', '".$type_id."', '".$review_rating."', '".$review_body."');";
    $response_reviews = $mydb->query($query_reviews);
	
	// error handling: check for valid response
	if (!$response_reviews) {
		return createError("Failed to retrieve records from table 'reviews'");
	}
	
	// pack up data to return
    $review_data = array(
		'status' => 'ok',
		'message' => 'Review posted'
	);
    while ($row = $response_reviews->fetch_assoc()) {
        $review_data['reviews'][] = $row;
    }

    // Return the reviews array
    echo "Posted: ".$review_rating." star review by user ".$user_id;
    $mydb->close();
    
    return $review_data;
}

//
// FETCH USER REVIEWS
//
function fetchUserReviews($auth_key) {
	// error handling: check auth key
	if (!isset($auth_key)) {
		return createError("No auth key provided");
	}

    // connect to database
	$mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work
    
    if ($mydb->errno != 0) {
    	// error hgandling: check for valid connection
		return createError("Failed to establish database connection");
    }
    echo "Successfully connected to database".PHP_EOL;
    
    // query the database
    $query_user = "select user_id from reviews where authkey = '".$auth_key."';";
    $response_user = $mydb->query($query_user);
    
    // error handling: check for valid response
	if (!$response_reviews) {
		return createError("Failed to retrieve records from table 'reviews'");
	}
	
	// pack up data to return
    $review = array();
    while ($row = $response_reviews->fetch_assoc()) {
        $review[] = $row;
    }
    
    
    $review_data = array(
		'status' => 'ok',
		'message' => 'Fetched service reviews',
		'reviews' => $review,
	);
    var_dump($review_data);

    // Return the reviews array
    echo "Returned: ".$response_reviews->num_rows." reviews of service ".$service_id;
    $mydb->close();
    
    
    //return array("status" => "ok");
    return $review_data;
}

//
// FETCH SERVICE REVIEWS
//
function fetchServiceReviews($service_id, $service_type) {
	// error handling: check service key
	if (!isset($service_id)) {
		return createError("No service id provided");
	}
	    
    // connect to database
	$mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work
    
    if ($mydb->errno != 0) {
    	// error hgandling: check for valid connection
		return createError("Failed to establish database connection");
    }
    echo "Successfully connected to database".PHP_EOL;

    // fetch service reviews
	$query_reviews = "SELECT firstName, lastName, type, review_date, review_body, review_rating, userID, typeID  FROM reviews join user on reviews.userID=user.ID  WHERE typeID =" . $service_id . " and type = '".$service_type."';";
    $response_reviews = $mydb->query($query_reviews);
	
	// error handling: check for valid response
	if (!$response_reviews) {
		return createError("Failed to retrieve records from table 'reviews'");
	}
	
	// pack up data to return
	$review = array();
	while($row = $response_reviews -> fetch_assoc()){
		$review[] = $row;
		
	}
	
	$review_data = array(
		'status' => 'ok',
		'message' => 'Posted new review',
		'reviews' => $review,
	);
	
	
    var_dump($review_data);

    // Return the reviews array
    echo "Returned: ".$response_reviews->num_rows." reviews of service ".$service_id;
    $mydb->close();
    
    
    //return array("status" => "ok");
    return $review_data;
}

//
// REQUEST PROCESSOR
//
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
      return doLogin($request['password'], $request['email']);
      
    case "register":
    	return doRegister($request['password'], $request['email'], $request['first_name'], $request['last_name'] );
    	
    case "validate_session":
      return doValidate($request['accesskey']);
      
  	case "post_review":
      return postReview($request['auth_key'], $request['service_type'], $request['type_id'], $request['user_id'], $request['review_rating'], $request['review_body']);
      
	case "get_user_reviews":
      return fetchUserReviews($request['auth_key']);
      
  	case "get_service_reviews":
      return fetchServiceReviews($request['service_id'], $request['service_type'] );
    
    default:
    	return array("returnCode" => '1', 'message' => "ERROR: unsupported message type", 'status'=>'error');
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed. Unknown message type provided.");
}

//
// CREATE ERROR MESSAGE
//

function createError($mydb=null, $error_description) {
	if($mydb){
		echo $error_description . ": ". $mybd->error  .  PHP_EOL;
		return array(
		'status'=> "error",
		'error'=> $error_description);
	}
	else{
		echo $error_description . ": " .  PHP_EOL;
		return array(
		'status'=> "error",
		'error'=> $error_description);
	}
	exit(0);
}




$server = new rabbitMQServer("testRabbitMQ.ini","testServer");
echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
exit();
?>

