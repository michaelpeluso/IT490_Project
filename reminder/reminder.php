#!/usr/bin/php
<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require './PHPMailer/src/Exception.php';
    require './PHPMailer/src/PHPMailer.php';
    require './PHPMailer/src/SMTP.php';

    $mydb = new mysqli('127.0.0.1','register','pwd','IT490');// <-- ip may have to be changed if it does not work

    if ($mydb->errno != 0)	
    {
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
    }

    echo "successfully connected to database".PHP_EOL;
    
    // get current date time
    $currTime = new DateTime();
    $currTime -> setTime(0, 0, 0);
    $time1 = $currTime->format('Y-m-d H:i');
    $currTime->setTime(23, 59, 59);
    $time2 = $currTime->format('Y-m-d H:i');
    
    
    
    echo "".$time1." - ".$time2."\n";
    $query = "select * from email where subDate <= '".$time2."' and  subDate >= '".$time1."';";
    
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
	echo "no emails to send";
	return array(
	'status'=> "error",
	'error'=> "no emails to send");
    }
    
    $IDs = array();
    $IDs[] = "0";
    while ($row = $response -> fetch_assoc()){
    
    	if($row["sent"] === "0"){
    
	    	$mail = new PHPMailer(true);
		$mail ->isSMTP();
		$mail ->Host='smtp.gmail.com';
		$mail ->SMTPAuth= true;
		$mail ->Username = 'triptellercompany@gmail.com';
		$mail ->Password = 'frgk chzi bxmw kkfz';
		$mail ->Port = 465;
		$mail ->SMTPSecure = "ssl";
		$mail ->isHTML(false);
		$mail ->setFrom("triptellercompany@gmail.com" , "Tripteller");

		$mail ->addAddress("".$row['reciverAdd']);
		$mail ->Subject= "".$row['subject'];
		$mail ->Body = "".$row['message'];
		$mail ->send();
		$IDs[] = $row["ID"];
	    	echo "email sent\n";
	    	
	    	
    	}
    };
    
    
    $query = "update email set sent=1 where ID in (".implode(",",$IDs).");";
    $mydb->query($query);
    

?>
