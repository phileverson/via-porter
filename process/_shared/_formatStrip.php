<?php
function curl_file_get_contents($url)
{
 $curl = curl_init();
 $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
 
 curl_setopt($curl,CURLOPT_URL,$url); //The URL to fetch. This can also be set when initializing a session with curl_init().
 curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
 curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5); //The number of seconds to wait while trying to connect.	
 
 curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
 curl_setopt($curl, CURLOPT_FAILONERROR, TRUE); //To fail silently if the HTTP code returned is greater than or equal to 400.
 curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
 curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
 curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.	
 
 $contents = curl_exec($curl);
 curl_close($curl);
 return $contents;
}



function formatStrip($ourPassID, $i_passSet) {

	$i_passSet = abs($i_passSet - 1);
	// echo $i_passSet;

    //creating the new strip image (setting the image size, background colour, etc.)
	$image = imagecreatefromjpeg('_passes/' . $ourPassID . '/' . $i_passSet . '/strip.png');

	$filename = 'strip.png';

	$thumb_width = (75 * 2);
	$thumb_height = (75 * 2);

	$width = imagesx($image);
	$height = imagesy($image);

	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;

	// getting the right dimenstions
	$new_height = $thumb_height;
	$new_width = $width / ($height / $thumb_height);

	$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

	$white = imagecolorallocate($thumb, 60, 65, 76);
	imagefill($thumb, 0, 0, $white);

	// Resize and crop to create a strip
	imagecopyresampled($thumb,
	                   $image,
	                   // 0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
	                   // 0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                       0 - (($new_width) - ($thumb_width)), // Align right
                       0 - ($new_height - $thumb_height) / 2, // Center the image vertically
	                   0, 0,
	                   $new_width, $new_height,
	                   $width, $height);

	//save the barcode image in the strip format
	imagepng($thumb, $filename, 0);
	$stripImagePath = './_passes/' . $ourPassID . '/' . $i_passSet . '/' . $filename;
	rename($filename, $stripImagePath);

	

if( strpos($_SERVER['HTTP_HOST'], '8888') > 0)
{
	$zxingPath = 'http://oi60.tinypic.com/4hval3.jpg';
}
else
{
	$zxingPath = 'http://grid.evertek.ca/deck4/via-porter/process' . substr($stripImagePath, 1);
}
	






	//pass file path to zxing to decode...
	$url = 'http://zxing.org/w/decode?u=' . $zxingPath;

		echo 'full path for zxing is:    ' . $url . '</br>';


	// using file_get_contents function
	$content = curl_file_get_contents($url);

	$preParse = explode('pre',$content);
	$arrowParse = explode('>',$preParse[1]);

	//the content of the code that's returned from zxing.org 
	$rawTextParsed = $arrowParse[1];

	$rawTextParsed = substr($rawTextParsed, 0, -3);
	return $rawTextParsed;

}
?>