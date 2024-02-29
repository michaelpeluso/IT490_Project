#!/usr/bin/php
<?php

$mydb = new mysqli('127.0.0.1','register','pwd','IT490');

if ($mydb->errno != 0)
{
	echo "failed to connect to database: ". $mydb->error . PHP_EOL;
	exit(0);
}

echo "successfully connected to database".PHP_EOL;

$query = "select * from user;";

$response = $mydb->query($query);
if($response -> num_rows()>1){
	return "username or email in use";
}
while ($row =$response -> fetch_assoc()){
	echo print_r($row);
}
if ($mydb->errno != 0)
{
	echo "failed to execute query:".PHP_EOL;
	echo __FILE__.':'.__LINE__.":error: ".$mydb->error.PHP_EOL;
	exit(0);
}


?>
