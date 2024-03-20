<!DOCTYPE>
<html>
	<head>
	<title>Login Form</title>
    <style>
    	body{
   		background-color: #BDE3FF;
   		height:100%;
   		width:100%;
   	}
   	.container{
   		display:flex;
   	
   	}
   	.logo{
   	    	width: 20%; 
           	margin-left: 5%;
           	margin-right: 5%;
           	margin-top:20%;
           	font-size:8em;
           	font-family:sans-serif;
   	}
   	a.test:link, a.test:visited, a.test:active{
        	color:black;
        	text-decoration:none;
   	}
   	a.test:hover{
   		color:green;
   	}
   	a.test2:link, a.test2:visited, a.test2:active{
        	color:blue;
   	}
   	a.test2:hover{
   		color:purple;
   	}
        .form-container {
            background-color: #FFFFFF;
            width: 25%;
            height: 40%; 
            margin: 10% auto; 
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 40px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); 
        }
	.error{
        	color:red;
        }
        label {
            display: block; 
            margin-bottom: -5px;
            margin-left: 5px;
   
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 20px); 
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 40px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            margin: 20px auto 0 auto; 
        }
    </style>
	</head>
	<body>
	<?php
//DEPENDENCIES
require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$email = $password ="";
$emailErr = $passwordErr = "";
$returnErr = "";
$valid = true;

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$email    = $_POST['email'];
	$password = $_POST['password'];
	
	if($password === ""){
		$passwordErr = "Must include a password.<br>";
		$valid = false;
	}
	if($email === ""){
		$emailErr = "Must include an email.<br>";
		$valid = false;
	}
	if(filter_var($email, FILTER_VALIDATE_EMAIL) == false){
		$emailErr = "Not a valid email.<br>";
		$valid = false;
	}
	if(strlen($password) <8){
		$passwordErr = $passwordErr."Passowrd must have at least 8 characters.<br>";
		$valid = false;
	}
	if(! preg_match("/[0-9]/",$password)){
		$passwordErr = $passwordErr."Passowrd must include number(s).<br>";
		$valid = false;
	}
	if(! preg_match("/[a-z]/",$password) || ! preg_match("/[A-Z]/",$password)){
		$passwordErr = $passwordErr."Passowrd must include lower and upper case letters.<br>";
		$valid = false;
	}
	if(! strpbrk($password, "!@#$%^&*()_+-=`~;:'\"\\|{[}]/?.>,<")){
		$passwordErr = $passwordErr."Passowrd must include special character(s).<br>";
		$valid = false;
	}
}


	?>
	
	<div class="container">
		<div class="logo">
		<a class="test" href="../">TRIPTELLER</a>
		</div>
		<div class="form-container">         <h2 style = "text-align: center;">Login</h2>
			<form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <?php 
        
        echo 	'
        <span class="error">'.$returnErr .'</span>
            <div>
        	<label for="email">Email:</label><br>
            	<input type="email" id="email" name="email" value="'.$email.'" >
		<span class="error">'.$emailErr .'</span><br><br>
	    </div>
	    <div>
            	<label for="password">Password:</label><br>
            	<input type="password" id="password" name="password" value="'.$password.'" >
            	<span class="error">'.$passwordErr .'</span><br><br>
            </div>
            <div>
            	<label for="sign up">Don\'t have an account yet? <a class="test2" href="../registration/register.php">sign up!</label><br>
	    </div>
	    <div>
            	<input type="submit" style = "align-items: center;"="Register" value="Register"> 
            </div>';
            	
        ?>
            
        </form>
			
			
		</div>
		</div>
	</body>
</html>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST" && $valid == true){
global $SODIUM_KEY;
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";

// retrieve user values

$password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);


// make connection
$client = new rabbitMQClient("../testRabbitMQ.ini","testServer");

//encrypt and encode
$SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
echo base64_encode($nonce);

$encrypted_email = sodium_crypto_secretbox( $email, $nonce, $SODIUM_KEY);
$encrypted_pass = sodium_crypto_secretbox( $password, $nonce, $SODIUM_KEY);

$encode_email = base64_encode($nonce . $encrypted_email);
$encode_pass  = base64_encode($nonce . $encrypted_pass );

echo ("before");
// request
$request = array(
	'type' => "login",
	'password' => $encode_pass,
	'email' => $encode_email,
	'message' => "login attepmt made by "
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
if ($response['status'] === "ok"){
	session_start();
	$_SESSION["key"] = $response['key'];
	$_SESSION["user_id"] = $response['user_id'];
	$_SESSION["first_name"] = $response['first_name'];
	$_SESSION["last_name"] = $response['last_name'];
	$_SESSION["email"] = $response['email'];
	header("Location: http://100.35.46.200/IT490_Project/registered/");
}else{
$returnErr = $response['message'];
}

}

?>
