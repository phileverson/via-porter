<?php
//Library for PKPass
require('./_extras/PKPass/PKPass.php');

function createPassFile($ourPassID, $i_passSet, $passDetails)
{
    $pass = new PKPass\PKPass();

    $pass->setCertificate('./_extras/PKPass/Cert-viaporter-1.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
    $pass->setCertificatePassword('Philip99');     // 2. Set password for certificate
    $pass->setWWDRcertPath('./_extras/PKPass/AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)

    // Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
    $standardKeys         = array(
        'formatVersion'      => 1,
    	'authenticationToken' => '1234567890abcdef1234',
        'logoText'   => 'CanTravel ' . $passDetails[1],
        "organizationName" => "CanTravel Pass",
        'description' => 'No afilication with VIA Rail, Porter Airlines, or any other transportation providers.',
        'passTypeIdentifier' => 'pass.via-porter', // 4. Set to yours
        'serialNumber'       => '123456',
        'teamIdentifier'     => 'GH2A55GQ4M'           // 4. Set to yours
    );
    $associatedAppKeys    = array();
    $relevanceKeys        = array();
    $styleKeys            = array(
        'boardingPass' => array(
            'transitType' => 'PKTransitTypeTrain',
            'primaryFields' => array(
                array(
                    'key'   => 'originStation',
                    'label' => 'DEPARTING',
                    'value' => $passDetails[7]
                ),
                array(
                    'key'   => 'destinationStation',
                    'label' => 'ARRIVING',
                    'value' => $passDetails[8]
                ),
            ),
            'auxiliaryFields' => array(
                array(
                    'key'   => 'originStationFullName',
                    'label' => '',
                    'value' => $passDetails[3]
                ),
                array(
                    'key'   => 'destinationStationFullName',
                    'label' => '',
                    'value' => $passDetails[5]
                )
            ),
            'secondaryFields' => array(
                array(
                    'key'   => 'origin',
                    'label' => 'DEPARTURE DETAILS',
                    'value' => $passDetails[4],
                    'textAlignment' => 'PKTextAlignmentLeft'
                ),
                array(
                    'key'   => 'destination',
                    'label' => 'ARIVAL DETAILS',
                    'value' => $passDetails[6],
                    'textAlignment' => 'PKTextAlignmentRight'
                )
            ),
            'headerFields' => array(
                array(
                    'key'   => '1',
                    'label' => 'Seat',
                    'value' => '5S'
                ),
                array(
                    'key'   => '0',
                    'label' => 'Car',
                    'value' => '1'
                )
            ),
            'backFields' => array(
                array(
                    'key'   => 'passenger-name',
                    'label' => 'Passenger',
                    'value' => $passDetails[2]
                )
            )
        ),
        'barcode' => array(
            'format'   => 'PKBarcodeFormatAztec',
            // 'message' => '1811201424352Everson                       3   9C CWLLTRTOVIA65  201412071305Phil                P1N YTHEHQ81020141118124602ES NB ',//$passDetails[0],
            'message' => $passDetails[0] . ' ',
            'messageEncoding' => 'iso-8859-1'
        )
    );
    $visualAppearanceKeys = array(
        'backgroundColor' => 'rgb(91, 80, 81)',
        // 'backgroundColor' => 'rgb(60, 65, 76)',
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
    $pass->addFile('_images/icon.png');
    $pass->addFile('_images/icon@2x.png');
    $pass->addFile('_images/logo.png');
    $pass->addFile('_images/logo@2x.png');

    $pass->create(true, $ourPassID, $i_passSet);
}