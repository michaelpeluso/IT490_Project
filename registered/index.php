<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<?php 
	session_start();
	if(!isset($_SESSION["key"])){	
		header("Location:  http://100.35.46.200/IT490_Project/");
		//validate key
	}?>
	<H1>Welcome to Tripteller!</H1>
	<H3>How are you <?php echo ($_SESSION["first_name"]. " ". $_SESSION["last_name"])  ?> ?</H3>
	<form method="get" action="logout.php">
	<input type="submit" value="Logout">
	</form>
	<?php echo ("this is php");?>
</body>
</html>
