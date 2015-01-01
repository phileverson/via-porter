<?php
function formatStrip($ourPassID, $i_passSet) {
    //creating the new strip image (setting the image size, background colour, etc.)
	$image = imagecreatefromjpeg('_passes/' . $ourPassID . '/' . $i_passSet . '/strip.png');

	$filename = 'strip.png';

	$thumb_width = (320 * 2);
	$thumb_height = (110 * 2);

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

	//returning the path to the image (for the pass creation)
	return $stripImagePath;
}
?>