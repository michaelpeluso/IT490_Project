<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
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
            margin:auto;
            margin-top: 1% auto; 
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

$email = $password = $first_name = $last_name  = $confPassword ="";
$emailErr = $passwordErr = $first_nameErr = $last_nameErr  = $confPasswordErr ="";
$valid = true;

if ($_SERVER["REQUEST_METHOD"]=="POST"){
	$email    = $_POST['email'];
	$password = $_POST['password'];
	$confPassword = $_POST['confPassword'];  
	$first_name = $_POST['fname'];
	$last_name  = $_POST['lname'];
	
	
	if($first_name === ""){
		$first_nameErr = "Must include a first name.<br>";
		$valid = false;
	}
	if($last_name === ""){
		$last_nameErr = "Must include a last name.<br>";
		$valid = false;
	}
	if($password === ""){
		$passwordErr = "Must include a password.<br>";
		$valid = false;
	}
	if($email === ""){
		$emailErr = "Must include an email.<br>";
		$valid = false;
	}
	if($confPassword === ""){
		$confPasswordErr = "Must include a confirmation password.<br>";
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
	if(!($password == $confPassword)){
		$confPasswordErr = "Passowrds must be equal<br>";
		$valid = false;
	}
}


	?>
	<div class="container">
		<div class="logo"><a class="test" href="../">
		TRIPTELLER</a>
		</div>
    <div class="form-container">         <h2 style = "text-align: center;">Register</h2>
	
        <form action="<?php  echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
        <?php 
        
        echo 	'<label for="fname">First Name:</label><br>
	        <input type="text" id="fname" name="fname"  value="'.$first_name.'" >
	        <span class="error">'.$first_nameErr.'</span><br><br>

            	<label for="lname">Last Name:</label><br>
            	<input type="text" id="lname" name="lname" value="'.$last_name.'" >
		<span class="error">'.$last_nameErr.'</span><br><br>

            	<label for="email">Email:</label><br>
            	<input type="email" id="email" name="email" value="'.$email.'" >
		<span class="error">'.$emailErr .'</span><br><br>

            	<label for="password">Password:</label><br>
            	<input type="password" id="password" name="password" value="'.$password.'" >
            	<span class="error">'.$passwordErr .'</span><br><br>
            
            	<label for="password">Confirm Password:</label><br>
            	<input type="password" id="confPassword" name="confPassword" value="'.$confPassword.'" >
            	<span class="error">'.$confPasswordErr .'</span><br><br>
            
	    	<label for="login">Already have an account yet? <a class="test2" href="../login/login2.php">login!</label><br>
	    
            	<input type="submit" style = "align-items: center;"="Register" value="Register"> ';
        ?>
            
        </form>
    </div> 
    </div>
</body>
</html>
<?php
if($_SERVER["REQUEST_METHOD"]=="POST" && $valid == true){
// Input Handling and Basic Sanitization
$email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$password = filter_var($_POST['password'], FILTER_SANITIZE_EMAIL);
$confPassword = filter_var($_POST['confPassword'], FILTER_SANITIZE_EMAIL);  
$first_name = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
$last_name  = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);

// Hashing
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
//encrypting and encoding
$SODIUM_KEY_hex = "316d84ecd4bfd5c19ff9b3ad48c2780d8553a23a60a22d1e14c583decbd6fea9";
$SODIUM_KEY = sodium_hex2bin($SODIUM_KEY_hex);
$nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

$encrypted_email = sodium_crypto_secretbox( $email, $nonce, $SODIUM_KEY);

$encode_email = base64_encode($nonce . $encrypted_email);

// eventually we should change this to use ENVIORNMENT variables instead
$request = array(
    'type' => "register",
    'email' => $encode_email,
    'password' => $hashed_password,
    'first_name' => $first_name,
    'last_name' => $last_name,
);

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

echo "Server response: \n";
print_r($response);

if ($response['status'] === "ok"){
	header("Location:  http://100.35.46.200/IT490_Project/login/login2.php");
}
}

?>