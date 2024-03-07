#!/usr/bin/php
<?php

# dependencies
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$functions = json_decode(file_get_contents('function_descriptions.json'), true);


# gather user input
if (isset($_POST['Submit'])) {
	$botpromt = $_POST['botpromt'];
	$query = $_POST['query'];
}

# connect to API
$apiKey = $_ENV["CHATGPT_API_KEY"];
$client = OpenAI::client($apiKey);


# generarte a completion
$prompt = "Get me the details of the best flight from Amsterdam to New York occuring in a week from today.";

$response = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => $prompt],
    ],
    'functions' => $functions,
    'function_call' => 'auto'
]);

echo $response->usage->totalTokens . " tokens: " . $response->choices[0]->message->functionCall->arguments;


/*
# start a chat
$query = "My name is Michael.";

$result = $client->chat()->create([
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'user', 'content' => $query],
    ],
]);


echo $result->choices[0]->message->content;
*/

echo "\n"


?>
