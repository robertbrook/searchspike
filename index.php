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
        span.host {display:block;color:gray;}
        span.info {display:block;color:gray;}
        span.tag {padding-right:1rem;}
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

preg_match("/https:\/\/petition.parliament.uk\/petitions\/\d*$/", $value->url, $petition_match);

if (count($petition_match) == 1) {
    $petition_json = file_get_contents($value->url . ".json");
    $petition_obj = json_decode($petition_json);
    $petition_attrs = $petition_obj->data->attributes;
    print_r($petition_attrs);
}
    
$tags = get_meta_tags($value->url);

$article_host = parse_url($value->url, PHP_URL_HOST);

$article_snippet = $value->snippet;
if (isset($tags['description'])) { $article_snippet = $tags['description']; }
if (isset($tags['twitter:description'])) { $article_snippet = $tags['twitter:description']; }

$article_title = $value->name;
if (isset($tags['citation_title'])) { $article_title = $tags['citation_title']; }
if (isset($tags['twitter:title'])) { $article_title = $tags['twitter:title']; }
if (isset($petition_attrs->action)) { $article_title = "<q>" . $petition_attrs->action . "</q>"; }

$article_author = "";
if (
    isset($tags['citation_author']) === true && $tags['citation_author'] !== ''
    ) { $article_author = "<span class='tag'>Author <b>" . $tags['citation_author'] . "</b></span>"; }

$article_topic = "";
if (
    isset($tags['citation_topic']) === true && $tags['citation_topic'] !== ''
    ) { $article_topic = "<span class='tag'>Topic <b>" . $tags['citation_topic'] . "</b></span>"; }

$article_section = "";
if (
    isset($tags['citation_section']) === true && $tags['citation_section'] !== ''
    ) { $article_section = "<span class='tag'>Section <b>" . $tags['citation_section'] . "</b></span>"; }

$twitter_site = "";
if (
    isset($tags['twitter:site']) === true && $tags['twitter:site'] !== ''
    ) { $twitter_site = "<span class='tag'>Twitter <b>" . $tags['twitter:site'] . "</b></span>"; }

echo <<<ARTICLE
<article id="$value->id">
<big><a href="$value->url">$article_title</a></big>
<span class="host">$article_host</span>
<span class="displayUrl">$value->displayUrl</span>
$article_snippet
<span class="info">
    $article_author
    $article_topic 
    $article_section
</span>
</article>
ARTICLE;
    
}

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
