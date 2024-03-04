#!/usr/bin/php
<?php

# dependencies
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# gather user input
if (isset($_POST['Submit'])) {
	$botpromt = $_POST['botpromt'];
	$query = $_POST['query'];
}
/***/
$query = "My name is Michael.";
/***/

# connect to API
$apiKey = $_ENV["CHATGPT_API_KEY"];
$client = OpenAI::client($apiKey);

# get query result
$result = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => $query],
    ],
]);


echo $result->choices[0]->message->content;
echo "\n"


?>
