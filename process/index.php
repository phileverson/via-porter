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

//catch so that we only check for post data on prod
if( strpos($_SERVER['HTTP_HOST'], '8888') > 0)
{
    echo '</br> Not looking for file in post...</br>';
    $file = 'inbound.json'; //copying the JSON rather then saving a new file with the post data (temp)

}
else
{
    file_put_contents("inbound_via_train_update.json", file_get_contents("php://input"));
    $file = 'inbound_via_train_update.json'; //copying the JSON rather then saving a new file with the post data (temp)
}


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

echo '<h2 class="fromEmail">'.$fromEmail.'</h2>';

//saving the images and getting their filenames in an array
$barcodes = getStrips($ourPassID, $inbound);


//********************************************************************
//BIG For Loop for Creating the Pass (s)
//********************************************************************

for ($i=0; $i < (count($barcodes)); $i++) { 

    //Parsing the data we need the pass fields:
    // $passengerName = ucwords(getPassDataPost(getPassDataPrePost($emailTextVersion, 'PASSENGER : ', 'VIA PR', $i), ','));
    // $trainNum = trim(strip_tags(getPassDataPrePost($emailTextVersion, 'Train #', 'Carrier', $i)));
    $stripImageBarcode = formatStrip($ourPassID, $i);

    // // echo $emailTextVersion;
    // // $car;
    // // $seat;

    // //complicated parsing for date/times and to/froms
    // $viaRailToFromStringSingle = getPassDataPrePost($emailTextVersion, 'FTR : ', 'Train', $i);
    // // echo $emailTextVersion . '</br></br></br>string passed: ' . $viaRailToFromStringSingle;
    // $viaRailToFrom = viaRailToFrom($viaRailToFromStringSingle);

    $viaRailToFromVIABarCode = viaRailToFromVIABarCode($stripImageBarcode);

    $viaRailSeatCarStuffFromBarCode = viaRailSeat($stripImageBarcode);

    $viaRailAll = newMethodForAll($stripImageBarcode);

    //putting everything we need in an array for createPass()
    $passDetails = array(); //array that holds everything for the pass
    $passDetails[0] = $stripImageBarcode;
    $passDetails[1] = $viaRailAll[2]; //$trainNum;
    $passDetails[2] = $viaRailAll[0] . ' ' . $viaRailAll[1]; //$passengerName;
    $passDetails[3] = ''; //$viaRailToFrom[0]; //departure city
    $passDetails[4] = $viaRailAll[3]; //$viaRailToFrom[1]; //departure date 
    $passDetails[5] = ''; //$viaRailToFrom[2]; //aRrival city
    $passDetails[6] = ''; //$viaRailToFrom[3]; //aRrival date and time
    $passDetails[7] = $viaRailToFromVIABarCode[0]; //departure via city code
    $passDetails[8] = $viaRailToFromVIABarCode[1]; //arRival via city code
    $passDetails[9] = $viaRailSeatCarStuffFromBarCode[0]; //seat
    $passDetails[10] = $viaRailSeatCarStuffFromBarCode[1]; //car
    $passDetails[11] = $viaRailAll[4]; //departure time 


    createPassFile($ourPassID, $i, $passDetails);
    // echo '<a href="_passes/' . $ourPassID . '/' . $i . '/' . $ourPassID . '.pkpass">Click here to download the pass...</a></br></br>';
    echo '<a class="pass-path" trainNumVar="' . $viaRailAll[2] . '" href="../access/?passID=' . $ourPassID .'&i=' . $i . '">Click Here To Download Pass</a> </br></br>';


//********************************************************************
// SENDING NOTIFICATION EMAIL
//********************************************************************
if( strpos($_SERVER['HTTP_HOST'], '8888') > 0)
{
    echo '</br> ...No email being sent, as we\'re on local... </br>';
}
else
{
    //having the notification email sent...
    $sent = send_email(array(
        'to' => $fromEmail,
        'from' => 'CanTravel <pass@cantravel.co>',
        'subject' => 'CanTravel - Train ' . $passDetails[1],
        // 'text_body' => 'Click the link below to download your pass. http://cantravel.co/access/?passID=' . $ourPassID .'&i=' . $i . ' .',
        'html_body' => '<html><body><h1>CanTravel - Train ' . $passDetails[1] . '</h1><p><a href="http://cantravel.co/access/?passID=' . $ourPassID .'&i=' . $i . '">Click here to download your personal iOS boarding pass.</a> </p><p><em>CanTravel Team</em></p></body></html>'
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
}   


//********************************************************************
// WRITING REFERENCE TO PASS TO FIREBASE
//********************************************************************
$dataForCurl = ' {"passEmail": "'. $fromEmail . '", "passPath": "http://www.cantravel.co/access/?passID='. $ourPassID .'&i=' . $i . '", "passSentDate": "'. date('Y/m/d')  . '", "passTrainNum": "'. $passDetails[1] . '"}';

echo put('https://via-porter.firebaseio.com/passes.json', $dataForCurl);

} // closing huge for loop


function put($url, $fields)
{
    $post_field_string = http_build_query($fields, '', '&');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $response = curl_exec($ch);
    curl_close ($ch);
    
    return $response;
}


echo '</br>end of file, pass(s) in theory should be made...';

?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<h1>Phil's Awesome VIA Rail / Porter Airlines Passbook Creator</h1>

</body>
</html>
