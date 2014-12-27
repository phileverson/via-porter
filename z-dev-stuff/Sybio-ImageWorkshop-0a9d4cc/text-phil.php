<?php 

require_once('/src/PHPImageWorkshop/Core/ImageWorkshopLayer.php');
require_once('/src/PHPImageWorkshop/Exception/ImageWorkshopException.php');

// myscript.php:
require_once('/src/PHPImageWorkshop/ImageWorkshop.php'); // Be sure of the path to the class

// $layer = new PHPImageWorkshop\ImageWorkshop::initXXX(...);

// We initialize the norway layer from the picture norway.jpg
$norwayLayer = ImageWorkshop::initFromPath('norway.jpg');
 
// We initialize the watermark layer from the picture watermark.png
$watermarkLayer = ImageWorkshop::initFromPath('watermark.png');


// We add the watermark in the sublayer stack of $norwayLayer
$norwayLayer->addLayerOnTop($watermarkLayer, 12, 12, "LB");

$image = $norwayLayer->getResult(); // This is the generated image !
 
header('Content-type: image/jpeg');
imagejpeg($image, null, 95); // We choose to show a JPG with a quality of 95%
exit;

?>