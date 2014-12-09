<?php
require('PKPass.php');

//$tempQ = $GET_['key'];



//$url = 'http://zxing.org/w/decode?u=http://picpaste.com/pics/viatest-yNt4taEn.1417981434.jpg';
// $url = 'http://zxing.org/w/decode?u=http://picpaste.com/pics/viatest_65-HxIpqYvM.1417981361.jpg';

$url = 'http://zxing.org/w/decode?u=http://oi60.tinypic.com/4hval3.jpg';

// using file_get_contents function
$content = file_get_contents($url);

$preParse = explode('pre',$content);
$arrowParse = explode('>',$preParse[1]);

//the content of the code that's returned from zxing.org 
$rawTextParsed = $arrowParse[1];
//echo $rawTextParsed;








$pass = new PKPass\PKPass();

$pass->setCertificate('Cert-viaporter-1.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
$pass->setCertificatePassword('Philip99');     // 2. Set password for certificate
$pass->setWWDRcertPath('AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)

// Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
$standardKeys         = array(
    'description'        => 'Demo pass',
    'formatVersion'      => 1,
	'authenticationToken' => '1234567890abcdef1234',
    'organizationName'   => 'Flight Express',
    'passTypeIdentifier' => 'pass.via-porter', // 4. Set to yours
    'serialNumber'       => '123456',
    'teamIdentifier'     => 'GH2A55GQ4M'           // 4. Set to yours
);
$associatedAppKeys    = array();
$relevanceKeys        = array();
$styleKeys            = array(
    'boardingPass' => array(
        'primaryFields' => array(
            array(
                'key'   => 'origin',
                'label' => 'San Francisco',
                'value' => 'SFO'
            ),
            array(
                'key'   => 'destination',
                'label' => 'London',
                'value' => 'LHR'
            )
        ),
        'secondaryFields' => array(
            array(
                'key'   => 'gate',
                'label' => 'Gate',
                'value' => 'F13'
            ),
            array(
                'key'   => 'date',
                'label' => 'Departure date',
                'value' => '07/11/2012 10:22'
            )
        ),
        'backFields' => array(
            array(
                'key'   => 'passenger-name',
                'label' => 'Passenger',
                'value' => 'Phil Everson'
            )
        ),
        'transitType' => 'PKTransitTypeAir'
    )
);
$visualAppearanceKeys = array(
    'barcode'         => array(
        'format'          => 'PKBarcodeFormatAztec',
        'message'         =>  $rawTextParsed, //=> '1811201424352Everson                       3   9C CWLLTRTOVIA65  201412071305Phil                P1N YTHEHQ81020141118124602ES NB'//,
       'messageEncoding' => 'iso-8859-1'
    ),
    'backgroundColor' => 'rgb(107,156,196)',
    'logoText'        => 'Flight info'
);
$webServiceKeys       = array();

// Merge all pass data and set JSON for $pass object
$passData = array_merge(
    $standardKeys,
    $associatedAppKeys,
    $relevanceKeys,
    $styleKeys,
    $visualAppearanceKeys,
    $webServiceKeys
);

$pass->setJSON(json_encode($passData));

// Add files to the PKPass package
$pass->addFile('images/icon.png');
$pass->addFile('images/icon@2x.png');
$pass->addFile('images/logo.png');

if(!$pass->create(true)) { // Create and output the PKPass
    echo 'Error: '.$pass->getError();
}