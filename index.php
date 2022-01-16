<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        main {padding:2rem 0;}
    </style>
 </head>
 <body>
  <header>
<form action="index.php" method="GET">
 <input type="search" name="q">
    <input type="submit">
</form>
</header>
  <main>
<?php 

$apikey = getenv("APIKEY");

$q = htmlspecialchars($_GET['q']);


if (empty($q)) {
    echo '<!-- no q -->';
} else {
 

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "Ocp-Apim-Subscription-Key: ${apikey}\r\n"
    ]
];

$context = stream_context_create($opts);

$json = file_get_contents("https://api.cognitive.microsoft.com/bing/v7.0/search?q=${q}+site:parliament.uk&count=50&textDecorations=true&textFormat=HTML", false, $context);
$obj = json_decode($json);
print_r($obj->webPages->value);

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
