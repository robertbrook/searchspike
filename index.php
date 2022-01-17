<?php $q = htmlspecialchars($_GET['q']); ?><!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
        main {padding:2rem 0;}
        article {display:block;padding:0.75rem 0;}
        span.displayUrl {display:block;color:green;}
        span.info {display:block;color:gray;}
    </style>
 </head>
 <body>
  <header>
<form action="index.php" method="GET">
 <input type="search" name="q" value="<?php echo $q; ?>"><input type="submit" value="Search">
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

$json = file_get_contents("https://api.cognitive.microsoft.com/bing/v7.0/search?q=${q}+site:parliament.uk&count=50&textDecorations=true&textFormat=HTML", false, $context);
$obj = json_decode($json);

    
    // str_ends_with
    
foreach ($obj->webPages->value as $value) {
    
$tags = get_meta_tags($value->url);
    
print_r($tags);

$article_snippet = $value->snippet;
if (isset($tags['twitter:description'])) { $article_snippet = $tags['twitter:description']; }
if (isset($tags['description'])) { $article_snippet = $tags['description']; }

$article_title = $value->name;
if (isset($tags['citation_title'])) { $article_title = $tags['citation_title']; }
if (isset($tags['twitter:title'])) { $article_title = $tags['twitter:title']; }

$article_author = "";
if (
    isset($tags['citation_author']) === true && $tags['citation_author'] !== ''
    ) { $article_author = "Author <b>" . $tags['citation_author'] . "</b>"; }

$article_topic = "";
if (
    isset($tags['citation_topic']) === true && $tags['citation_topic'] !== ''
    ) { $article_topic = "&middot; Topic <b>" . $tags['citation_topic'] . "</b>"; }

$article_section = "";
if (
    isset($tags['citation_section']) === true && $tags['citation_section'] !== ''
    ) { $article_section = "&middot; Section <b>" . $tags['citation_section'] . "</b>"; }

echo <<<ARTICLE
<article id="$value->id">
<big><a href="$value->url">$article_title</a></big>
<span class="displayUrl">$value->displayUrl</span>
$article_snippet
<span class="info">$article_author $article_topic $article_section</span>
</article>
ARTICLE;
    
}

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
