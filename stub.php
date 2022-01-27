<?php $q = htmlspecialchars($_GET['q']); ?><!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {font-family:sans-serif;max-width:40rem;margin:1rem auto;line-height:1.4;padding:1rem;}
        main {padding:2rem 0;}
        article {display:block;padding:0.75rem 0;}
        span.host {color:green;font-weight:bold;}
        span.displayUrl {display:block;color:gray;}
        span.info {display:block;color:gray;}
        span.inline-info {color:gray;}
        span.tag {padding-right:0.5rem;}
        a {text-decoration:none;}
    </style>
 </head>
 <body>
  <header>
      <form action="stub.php" method="GET">
 <input type="search" name="q" id="search" value="<?php echo $q; ?>"> <input type="submit" value="Search">
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
    
$q = rawurlencode($q);

$json = file_get_contents("https://api.cognitive.microsoft.com/bing/v7.0/search?q=${q}+site:parliament.uk&count=50", false, $context);
$obj = json_decode($json);

    // check archived petitions
    // str_ends_with
    // petition depts
    
foreach ($obj->webPages->value as $value) {


$doc = new DOMDocument();
$doc->loadHTMLfile($value->url);
$title = $doc->getElementsByTagName( "title" );

$metas = $doc->getElementsByTagName( "meta" );

    echo "<br><br><p><a href='$value->url'>$value->url</a></p>";

    echo "<h1>$title</h1>";
    
foreach($metas as $meta) {
        echo "<pre>" . $meta . "</pre>";

    echo "<pre>" . $meta->getAttribute('name') . "</pre>";
    echo "<pre>" . $meta->getAttribute('content') . "</pre>";
    echo "<pre>" . $meta->getAttribute('property') . "</pre>";
}
      
}

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
