<?php

//Functions that actually produce the passbook pass:
require('_shared/_createPass.php');
//Needed for the Image
require('_extras/postmark-inbound-php-master/lib/Postmark/Autoloader.php');
//Has our not so fancy parsing stuff in it
require('_shared/_parseJson.php');
//functions for barcode stuff
require('_shared/_getStrips.php');
require('_shared/_formatStrip.php');
//PostMark Email script for sending...:
require('_shared/_sendEmail.php');

//Setting the timezone
date_default_timezone_set('UTC');

//Declaring Some Variables
$ourPassID = 'pass-' . time().hash("CRC32", $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]);

//********************************************************************
//Saving the JSON and putting it in the right spot
//********************************************************************

//Creating and moving the pass directory
mkdir($ourPassID, 0777, true);
rename($ourPassID, '_passes/' . $ourPassID);

//temp - in prod this page will be hit with post data. write code that takes what's posted and puts it in a json
file_put_contents("inbound_via_train_update.json", file_get_contents("php://input"));
$file = 'inbound_via_train_update.json'; //copying the JSON rather then saving a new file with the post data (temp)


$newfile = 'posthook.json';
copy($file, $newfile);
$jsonLocation = '_passes/' . $ourPassID . '/' . $newfile;
rename($newfile, $jsonLocation);

//********************************************************************
//Dealing with the Email
//********************************************************************

//starting the auto loader to parse the email
\Postmark\Autoloader::register();

//this file should be the target of the callback you set in your postmark account
$inbound = new \Postmark\Inbound(file_get_contents('_passes/' . $ourPassID . '/posthook.json'));

//Setting the email text versionto a variable so we can use it in our for loop below
$emailTextVersion = $inbound->TextBody();

//grabbing the from email address...
$fromEmail = $inbound->FromEmail();

echo '<h2>'.$fromEmail.'</h2>';

//echo '</br></br></br>' . $emailTextVersion . '</br></br></br>'; //temp for parsing

//saving the images and getting their filenames in an array
$barcodes = getStrips($ourPassID, $inbound);

//********************************************************************
//BIG For Loop for Creating the Pass (s)
//********************************************************************

for ($i=0; $i < (count($barcodes)); $i++) { 

    //Parsing the data we need the pass fields:
    $passengerName = ucwords(getPassDataPost(getPassDataPrePost($emailTextVersion, 'PASSENGER : ', 'VIA PR', $i), ','));
    $trainNum = trim(strip_tags(getPassDataPrePost($emailTextVersion, 'Train #', 'Carrier', $i)));
    $stripImageBarcode = formatStrip($ourPassID, $i);

    // echo $emailTextVersion;
    // $car;
    // $seat;

    //complicated parsing for date/times and to/froms
    $viaRailToFromStringSingle = getPassDataPrePost($emailTextVersion, 'FTR : ', 'Train', $i);
    // echo $emailTextVersion . '</br></br></br>string passed: ' . $viaRailToFromStringSingle;
    $viaRailToFrom = viaRailToFrom($viaRailToFromStringSingle);

    $viaRailToFromVIABarCode = viaRailToFromVIABarCode($stripImageBarcode);

    //putting everything we need in an array for createPass()
    $passDetails = array(); //array that holds everything for the pass
    $passDetails[0] = $stripImageBarcode;
    $passDetails[1] = $trainNum;
    $passDetails[2] = $passengerName;
    $passDetails[3] = $viaRailToFrom[0]; //departure city
    $passDetails[4] = $viaRailToFrom[1]; //departure date and time
    $passDetails[5] = $viaRailToFrom[2]; //aRrival city
    $passDetails[6] = $viaRailToFrom[3]; //aRrival date and time
    $passDetails[7] = $viaRailToFromVIABarCode[0]; //departure via city code
    $passDetails[8] = $viaRailToFromVIABarCode[1]; //arRival via city code

    // echo '</br>arival city: ' . $passDetails[5] . '</br>';

    createPassFile($ourPassID, $i, $passDetails);
    // echo '<a href="_passes/' . $ourPassID . '/' . $i . '/' . $ourPassID . '.pkpass">Click here to download the pass...</a></br></br>';
    echo '<a href="../access/?passID=' . $ourPassID .'&i=' . $i . '">Click Here To Download Pass</a> </br></br>';


//having the notification email sent...
$sent = send_email(array(
    'to' => $fromEmail,
    'from' => 'CanTravel <phil@phileverson.com>',
    'subject' => 'CanTravel Pass: Train #' . $passDetails[1],
    'text_body' => 'Click the link below to download your pass. http://grid.evertek.ca/deck4/via-porter/access/?passID=' . $ourPassID .'&i=' . $i . ' .',
    'html_body' => '<html><body><a href="http://grid.evertek.ca/deck4/via-porter/access/?passID=' . $ourPassID .'&i=' . $i . '">Click Here To Download Pass</a> </br></br><em>CanTravel Team</em></body></html>'
), $response, $http_code);
// Did it send successfully?
if( $sent ) {
    echo 'The email was sent!';
} else {
    echo 'The email could not be sent!';
}
// Show the response and HTTP code
echo '<pre>';
echo 'The JSON response from Postmark:<br />';
print_r($response);
echo 'The HTTP code was: ' . $http_code;
echo '</pre>';




} // closing huge for loop

echo '</br>end of file, pass(s) in theory should be made...';

?>

<!DOCTYPE html>
<html>
<head><!-- 
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdn.firebase.com/js/client/2.0.6/firebase.js"></script>
 -->
    <script type="text/javascript">
    // Firebase Setup
    // var myFirebaseRef = new Firebase("https://via-porter.firebaseio.com/");
    // var passesRef = myFirebaseRef.child("passes");
    // $('.pass-path').each(function() {
    //     passesRef.push({
    //       passPath: $(this).text()
    //     });
    //     console.log('added pass path');
    // });
    </script>

    <title></title>
</head>
<body>
<h1>Phil's Awesome VIA Rail / Porter Airlines Passbook Creator</h1>

</body>
</html>
