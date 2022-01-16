<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        main {padding:2rem 0;}
        article {display:block;padding:1rem 0;}
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

    
foreach ($obj->webPages->value as $value) {
    // print_r($value);
    
/*
  [id] => https://api.cognitive.microsoft.com/api/v7/#WebPages.41
    [name] => Armed Forces Bill (Division 1: held on Wednesday 8 Dec ...
    [url] => https://hansard.parliament.uk/Lords/2021-12-08/division/0DE8BCEC-0317-4972-AD3B-CD42E7456A5C/ArmedForcesBill?outputType=Names
    [isFamilyFriendly] => 1
    [displayUrl] => https://hansard.<b>parliament.uk</b>/Lords/2021-12-08/division/0DE8BCEC-0317-4972-AD3B-CD42E...
    [snippet] => Division 1: held on Wednesday 8 December 2021. Download CSV file View within context of debate. This page shows the Hansard record for the division. You can also view the division on the Votes in<b> Parliament website.</b>
    [dateLastCrawled] => 2022-01-12T00:43:00.0000000Z
    [language] => en
    [isNavigational] => 
*/
echo <<<ARTICLE
<article id="$value->id">
<big><a href="$value->url">$value->name</a></big>
<br>
$value->displayUrl
<br>
$value->snippet
</article>
ARTICLE;
    
}

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
