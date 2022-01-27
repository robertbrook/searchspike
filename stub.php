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

$json = file_get_contents("https://api.cognitive.microsoft.com/bing/v7.0/search?q=${q}+site:parliament.uk&count=50&textDecorations=true&textFormat=HTML", false, $context);
$obj = json_decode($json);

    // check archived petitions
    // str_ends_with
    // petition depts
    
foreach ($obj->webPages->value as $value) {

preg_match("/https:\/\/petition.parliament.uk\/petitions\/\d*$/", $value->url, $petition_match);

$petition_attrs = new stdClass();
$petition_state = "";
$petition_signature_count = "";
$petition_creator_name = "";
$petition_topics = "";

if (count($petition_match) == 1) {
    $petition_json = file_get_contents($value->url . ".json");
    $petition_obj = json_decode($petition_json);
    $petition_attrs = $petition_obj->data->attributes;
    // print_r($petition_attrs);
    
    if (
        isset($petition_attrs->state) === true && $petition_attrs->state !== ''
        ) { $petition_state = "<span class='tag'>State <b>" . $petition_attrs->state . "</b></span>"; }
    
    if (
        isset($petition_attrs->topics) === true && count($petition_attrs->topics) > 0
        ) { $petition_topics = "<span class='tag'>Topics <b>" . implode(", ", $petition_attrs->topics) . "</b></span>"; }
            
    if (
        isset($petition_attrs->signature_count) === true && $petition_attrs->signature_count !== ''
        ) { $petition_signature_count = "<span class='tag'><b>" . $petition_attrs->signature_count . "</b> signatures</span>"; }
    
    
    if (
        isset($petition_attrs->creator_name) === true && $petition_attrs->creator_name !== ''
        ) { $petition_creator_name = "<span class='tag'>Creator <b>" . $petition_attrs->creator_name . "</b></span>"; }
                
}
    
$tags = get_meta_tags($value->url);

$article_host = parse_url($value->url, PHP_URL_HOST);

$article_host_badge = "";
$article_host_badge = match ($article_host) {
    'petition.parliament.uk' => "Petitions",
    'committees.parliament.uk' => "Committees",
    'questions-statements.parliament.uk' => "Written Questions, Answers and Statements",
    'bills.parliament.uk' => "Bills",
    'edm.parliament.uk' => "Early Day Motions",
    'commonslibrary.parliament.uk' => "House of Commons Library",
    'lordslibrary.parliament.uk' => "House of Lords Library",
    'publications.parliament.uk' => "Publications",
    'hansard.parliament.uk' => "Hansard",
    'www.parliament.uk' => "Parliament",
    'members.parliament.uk' => "Members",
    'researchbriefings.files.parliament.uk' => "Research Briefings",
    'depositedpapers.parliament.uk' => "Deposited Papers",
    'electionresults.parliament.uk' => "Election Results",
    default => $article_host,
};

$article_snippet = "";
$article_snippet = $value->snippet;
if (isset($tags['description'])) { $article_snippet = $tags['description']; }
if (isset($tags['twitter:description'])) { $article_snippet = $tags['twitter:description']; }
if (isset($petition_attrs->background)) { $article_snippet = "<span class='inline-info'>Background</span> " . $petition_attrs->background . "<br>"; }
if (isset($petition_attrs->additional_details)) { $article_snippet .= "<span class='inline-info'>Additional details</span> " . $petition_attrs->additional_details . "<br>"; }

$article_title = "";
$article_title = $value->name;
if (isset($tags['citation_title'])) { $article_title = $tags['citation_title']; }
if (isset($tags['twitter:title'])) { $article_title = $tags['twitter:title']; }
if (isset($petition_attrs->action)) { $article_title = $petition_attrs->action; }

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


echo "<p><strong>$value->url</strong></p>";

    if ($tags !== false) {
            echo "<table>";

foreach ($tags as $key => $value) {
    echo "<tr><td style='width:20%;'>$key</td><td style='width:80%;'>$value</td></tr>";
}
            echo "</table>";

    }
    
    
}

}
   
   ?>
  </main>
  <footer></footer>
 </body>
</html>
