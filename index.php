<?php 

$apikey = getenv("APIKEY");

$q = "woking";

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Ocp-Apim-Subscription-Key: ${apikey}\r\n"
    ]
];

$context = stream_context_create($opts);

$json = file_get_contents('https://api.cognitive.microsoft.com/bing/v7.0/search?q=${q}+site:parliament.uk&count=50&textDecorations=true&textFormat=HTML', false, $context);
$obj = json_decode($json);
print_r($obj);

