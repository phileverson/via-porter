<?php
//Functions that actually produce the passbook pass:
require('./_extras/SimonWaldherr-passkit/passkit.php');

//Setting the timezone
date_default_timezone_set('UTC');

//Variables we need
$ourPassID = 'pass-' . time().hash("CRC32", $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]);

//Creating and moving the pass directory
mkdir($ourPassID, 0777, true);
rename($ourPassID, '_passes/' . $ourPassID);

// ----- TEMP ----- Saving the JSON
$file = 'inbound.json'; //copying the JSON rather then saving a new file with the post data (temp)
$newfile = 'posthook.json';
copy($file, $newfile);
rename($newfile, '_passes/' . $ourPassID . '/' . $newfile);

//Saving the Image
require_once '_extras/postmark-inbound-php-master/lib/Postmark/Autoloader.php';
\Postmark\Autoloader::register();

// this file should be the target of the callback you set in your postmark account
$inbound = new \Postmark\Inbound(file_get_contents('_passes/' . $ourPassID . '/posthook.json'));
$barcodes = array();
$passCount = 0;

foreach($inbound->Attachments() as $attachment) {
    if(strpos($attachment->Name,'jpg') !== false)
    {
    	$data = base64_decode($attachment->Content);
    	mkdir('_passes/' . $ourPassID . '/' . $passCount);
    	file_put_contents('_passes/' . $ourPassID . '/' . $passCount . '/strip.png', $data);
    	$barcodes[$passCount] = 'strip.png';
    	$passCount = $passCount + 1;
    }
}

for ($i=0; $i < ($passCount); $i++) { 
	$image = imagecreatefromjpeg('_passes/' . $ourPassID . '/' . $i . '/' . $barcodes[$i]);

	$filename = $barcodes[$i];

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
	rename($filename, '_passes/' . $ourPassID . '/' . $i . '/' . $filename);
	$tempImagePath = './_passes/' . $ourPassID . '/' . $i . '/' . $filename;

    $placePassPath = './_passes/' . $ourPassID . '/' . $i . '/';

    echo $tempImagePath . ' - making images is done ... </br>'; 

// --- START COPY OF Simon Waldherr's version of PKPASS ---
echo 'before require';
    
$Certificates = array('AppleWWDRCA'  => './_extras/SimonWaldherr-passkit/certs/AppleWWDRCA.pem', 
                      'Certificate'  => './_extras/SimonWaldherr-passkit/certs/Certificate.p12', 
                      'CertPassword' => 'Philip99');

$ImageFiles = array('./_extras/SimonWaldherr-passkit/images/icon.png', 
    './_extras/SimonWaldherr-passkit/images/icon@2x.png', 
    './_extras/SimonWaldherr-passkit/images/logo.png',
    $tempImagePath); //this line is where we add the strip image

$TempPath = $placePassPath;

$JSON = '{
  "authenticationToken": "vxwxd7J8AlNNFPS8k0a0FfUFtq0ewzFdc",
  "backgroundColor": "rgb(60, 65, 76)",
  "description": "The Beat Goes On",
  "eventTicket": {
    "backFields": [
      {
        "key": "terms",
        "label": "TERMS AND CONDITIONS",
        "value": "Lorem Ipsum dolar sit amet. Curabitur blandit tempus porttitor. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Nullam quis risus eget urna mollis ornare vel eu leo. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Maecenas faucibus mollis interdum. Nullam id dolor id nibh ultricies vehicula ut id elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras mattis consectetur purus sit amet fermentum. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras mattis consectetur purus sit amet fermentum."
      }
    ],
    "primaryFields": [
      {
        "key": "event",
        "label": "EVENT",
        "value": "Phil\'s Train"
      }
    ],
    "secondaryFields": [
      {
        "key": "loc",
        "label": "LOCATION",
        "value": "Moscone West"
      }
    ]
  },
  "foregroundColor": "rgb(255, 255, 255)",
  "formatVersion": 1,
  "logoText": "passkit.php",
  "organizationName": "Apple Inc.",
  "passTypeIdentifier": "pass.com.apple.demo",
  "serialNumber": "123456",
  "teamIdentifier": "123ABCDEFG",
  "webServiceURL": "'.$webServiceURL.'"
}';

echo '</br>about to call echoPass';

echoPass(createPass($Certificates, $ImageFiles, $JSON, 'passtest', $TempPath));

echo '</br>called echoPass';

// --- END COPY OF PKPASS ---

} // closing the big for loop

echo '</br>end of file';

?>


<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdn.firebase.com/js/client/2.0.6/firebase.js"></script>

    <script type="text/javascript">
    // Firebase Setup
    var myFirebaseRef = new Firebase("https://via-porter.firebaseio.com/");
    var passesRef = myFirebaseRef.child("passes");
    $('.pass-path').each(function() {
        passesRef.push({
          passPath: $(this).text()
        });
        console.log('added pass path');
    });
    </script>

    <title></title>
</head>
<body>
<h1>Phil's Awesome VIA Rail / Porter Airlines Passbook Creator</h1>

</body>
</html>
