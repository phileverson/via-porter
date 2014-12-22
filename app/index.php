<?php
require('PKPass.php');

// // $url = 'http://zxing.org/w/decode?u=http://oi60.tinypic.com/4hval3.jpg';
// $url = 'http://zxing.org/w/decode?u=http://picpaste.com/extpics/S9UeP4K3.png';

// // using file_get_contents function
// $content = file_get_contents($url);

// $preParse = explode('pre',$content);
// $arrowParse = explode('>',$preParse[1]);

// //the content of the code that's returned from zxing.org 
// $rawTextParsed = $arrowParse[1];
$rawTextParsed = '1612201423137EVERSON                       4   2B KITCTRTOVIA88  201412162135PHIL                P1H YTHEOM50020141216041702EC NB';

if($rawTextParsed != null)
{
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
                    'label' => 'Departing',
                    'value' => 'KITCHENER'
                ),
                array(
                    'key'   => 'destination',
                    'label' => 'Ariving',
                    'value' => 'TORONTO UNION STATION'
                )
            ),
            'auxiliaryFields' => array(
                array(
                    'key'   => 'depart-date-time',
                    'label' => '',
                    'value' => '12/12/2014 21:35'
                ),
                array(
                    'key'   => 'arival-date-time',
                    'label' => '',
                    'value' => '12/12/2014 23:20'
                )
            ),
            'headerFields' => array(
                array(
                    'key'   => 'trainNum',
                    'label' => 'Train',
                    'value' => '88'
                )
            ),
            'backFields' => array(
                array(
                    'key'   => 'passenger-name',
                    'label' => 'Passenger',
                    'value' => 'Phil Everson'
                )
            ),
            'transitType' => 'PKTransitTypeTrain'
        )
    );
    $visualAppearanceKeys = array(
        // 'barcode'         => array(
        //     'format'          => 'PKBarcodeFormatAztec',
        //     'message'         =>  $rawTextParsed,
        //     'messageEncoding' => 'utf-8'
        //     // 'messageEncoding' => 'iso-8859-1'
        // ),
        'backgroundColor' => 'rgb(89,80,81)',
        'foregroundColor' => 'rgb(255,255,255)',
        'labelColor'      => 'rgb(255,255,255)'
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
    $pass->addFile('images/logo@2x.png');

    if(!$pass->create(true)) { // Create and output the PKPass
        echo 'Error: '.$pass->getError();
    }
}
else
{
    echo 'problem generating pass';
}