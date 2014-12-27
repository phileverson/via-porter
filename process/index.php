<?php

//Functions that actually produce the passbook pass:
require('./_extras/SimonWaldherr-passkit/passkit.php'); 
//Needed for the Image
require('_extras/postmark-inbound-php-master/lib/Postmark/Autoloader.php');
//Has our not so fancy parsing stuff in it
require('_shared/_parseJson.php');

//Setting the timezone
date_default_timezone_set('UTC');

//Variables we need
$ourPassID = 'pass-' . time().hash("CRC32", $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]);


//********************************************************************
//Saving the JSON and putting it in the right spot
//********************************************************************

//Creating and moving the pass directory
mkdir($ourPassID, 0777, true);
rename($ourPassID, '_passes/' . $ourPassID);

//temp
$file = 'inbound.json'; //copying the JSON rather then saving a new file with the post data (temp)
$newfile = 'posthook.json';
copy($file, $newfile);
$jsonLocation = '_passes/' . $ourPassID . '/' . $newfile;
rename($newfile, $jsonLocation);


//********************************************************************
//Dealing with the Email
//********************************************************************

//starting the auto loader to parse the email
\Postmark\Autoloader::register();

// this file should be the target of the callback you set in your postmark account
$inbound = new \Postmark\Inbound(file_get_contents('_passes/' . $ourPassID . '/posthook.json'));

//Setting the email text versionto a variable so we can use it in our for loop below
$emailTextVersion = $inbound->TextBody();

//variables we need 
$barcodes = array();
$passCount = 0;

//saving the images in the image to our passes directory
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

//********************************************************************
//BIG For Loop for Creating the Pass (s)
//********************************************************************

for ($i=0; $i < ($passCount); $i++) { 

    //creating the image
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
	rename($filename, '_passes/' . $ourPassID . '/' . $i . '/' . $filename);
	$tempImagePath = './_passes/' . $ourPassID . '/' . $i . '/' . $filename;


    //Parsing the data we need the pass fields:
    $passengerName = ucwords(getPassDataPost(getPassDataPrePost($emailTextVersion, 'PASSENGER : ', 'VIA PR', $i), ','));
    $trainNum = trim(strip_tags(getPassDataPrePost($emailTextVersion, 'Train #', 'Carrier', $i)));

    // --- START COPY OF Simon Waldherr's version of PKPASS ---
    $Certificates = array('AppleWWDRCA'  => './_extras/SimonWaldherr-passkit/certs/AppleWWDRCA.pem', 
          'Certificate'  => './_extras/SimonWaldherr-passkit/certs/Certificate.p12', 
          'CertPassword' => 'Philip99');

    $ImageFiles = array('./_images/icon.png', 
    './_images/icon@2x.png', 
    './_images/logo.png',
    $tempImagePath); //this line is where we add the strip image

    $placePassPath = './_passes/' . $ourPassID . '/' . $i . '/';
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
                "key": "passengerNam12e",
                "label": "PASSENGER",
                "value": "'. $passengerName .'"
            }
        ],
        "secondaryFields": [
            {
                "key": "trainNum",
                "label": "TRAIN",
                "value": "'. $trainNum .'"
            }
        ]
    },
    "foregroundColor": "rgb(255, 255, 255)",
    "formatVersion": 1,
    "logoText": "Boarding Pass",
    "organizationName": "Apple Inc.",
    "passTypeIdentifier": "pass.via-porter",
    "serialNumber": "123456",
    "teamIdentifier": "GH2A55GQ4M",
    "webServiceURL": "'. $webServiceURL .'"
    }';

    //actually creating the pass
    createPass($Certificates, $ImageFiles, $JSON, 'passtest', $TempPath);
    // --- END COPY OF PKPASS ---

} // closing the huge for loop

echo '</br>end of file, pass(s) in theory should be made...';

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
