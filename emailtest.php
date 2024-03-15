<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';



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

$mail ->addAddress('guivilatoro@gmail.com');
$mail ->Subject= "Testing mail";
$mail ->Body = "testing body";
$mail ->send();


?>
