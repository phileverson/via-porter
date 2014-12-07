<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<title>VIA-Porter Passbook Generator</title>
</head>

<h1>VIA-Porter Passbook Generator</h1>

<?php

//$url = 'http://zxing.org/w/decode?u=http://picpaste.com/pics/viatest-yNt4taEn.1417981434.jpg';
// $url = 'http://zxing.org/w/decode?u=http://picpaste.com/pics/viatest_65-HxIpqYvM.1417981361.jpg';

$url = 'http://zxing.org/w/decode?u=http://oi60.tinypic.com/4hval3.jpg';

// using file_get_contents function
$content = file_get_contents($url);

$preParse = explode('pre',$content);
$arrowParse = explode('>',$preParse[1]);

//the content of the code that's returned from zxing.org 
$rawTextParsed = $arrowParse[1];
echo $rawTextParsed;

include('example-temp.php');


//# Use the Curl extension to query Google and get back a page of results
//$url = "http://zxing.org/w/decode?u=http://picpaste.com/pics/viatest_65-HxIpqYvM.1417980524.jpg";
//$ch = curl_init();
//$timeout = 5;
//curl_setopt($ch, CURLOPT_URL, $url);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//$html = curl_exec($ch);
//curl_close($ch);
//
//# Create a DOM parser object
//$dom = new DOMDocument();
//
//# Parse the HTML from Google.
//# The @ before the method call suppresses any warnings that
//# loadHTML might throw because of invalid HTML in the page.
//@$dom->loadHTML($html);
//
//# Iterate over all the <a> tags
//foreach($dom->getElementsByTagName('div') as $link) {
//        # Show the <a href>
//        echo $link->getAttribute('href');
//        echo "<br />";
//}
//
//echo "hello";
?>

<body>
</body>
</html>
