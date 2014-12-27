<?php
// Create a blank image and add some text
// $ini_filename = 'image-resize-test.png';

// $size = getimagesize('image-resize-test.png');

// print_r ($size);
// // $im = imagecreatefrompng($ini_filename );

// echo 'hi';

// $ini_x_size = getimagesize($ini_filename )[0];
// $ini_y_size = getimagesize($ini_filename )[1];

// //the minimum of xlength and ylength to crop.
// $crop_measure = min($ini_x_size, $ini_y_size);

// // Set the content type header - in this case image/jpeg
// //header('Content-Type: image/jpeg');

// $to_crop_array = array('x' =>0 , 'y' => 0, 'width' => $crop_measure, 'height'=> $crop_measure);
// $thumb_im = imagecrop($im, $to_crop_array);

// imagepng($thumb_im, 'thumb.png', 100);
// echo 'hi';

$image = imagecreatefromjpeg('strip3.jpg');

$filename = 'strip3-no-sharpen.png';

$thumb_width = (320 * 2);
$thumb_height = (123 * 2);

$width = imagesx($image);
$height = imagesy($image);

$original_aspect = $width / $height;
$thumb_aspect = $thumb_width / $thumb_height;

// getting the right dimenstions
$new_height = $thumb_height;
$new_width = $width / ($height / $thumb_height);

$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );

$white = imagecolorallocate($thumb, 255, 255, 255);
imagefill($thumb, 0, 0, $white);


// Resize and crop
imagecopyresampled($thumb,
                   $image,
                   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                   0, 0,
                   $new_width, $new_height,
                   $width, $height);

//save the image
imagepng($thumb, $filename, 0);

?>