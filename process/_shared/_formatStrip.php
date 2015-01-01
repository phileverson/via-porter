<?php
function formatStrip($ourPassID, $i_passSet) {
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
	$content = file_get_contents($url);

	$preParse = explode('pre',$content);
	$arrowParse = explode('>',$preParse[1]);

	//the content of the code that's returned from zxing.org 
	$rawTextParsed = $arrowParse[1];

	$rawTextParsed = substr($rawTextParsed, 0, -3);
	return $rawTextParsed;

}
?>