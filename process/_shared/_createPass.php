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
        'description'        => 'Demo pass',
        'formatVersion'      => 1,
    	'authenticationToken' => '1234567890abcdef1234',
        'organizationName'   => 'CDNTicket',
        'passTypeIdentifier' => 'pass.via-porter', // 4. Set to yours
        'serialNumber'       => '123456',
        'teamIdentifier'     => 'GH2A55GQ4M'           // 4. Set to yours
    );
    $associatedAppKeys    = array();
    $relevanceKeys        = array();
    $styleKeys            = array(
        'storeCard' => array(
            'primaryFields' => array(
                array(
                    'key'   => 'seat-and-car',
                    'label' => 'CAR / SEAT',
                    'value' => '1 / 12A'
                ),
            ),
            'secondaryFields' => array(
                array(
                    'key'   => 'origin',
                    'label' => 'DEPARTING',
                    'value' => 'TORONTO'//$passDetails[3]
                ),
                array(
                    'key'   => 'destination',
                    'label' => 'ARIVING',
                    'value' => $passDetails[5]
                )
            ),
            'auxiliaryFields' => array(
                array(
                    'key'   => 'depart-date-time',
                    'label' => 'DEPARTURE DETAILS',
                    'value' => $passDetails[4]
                ),
                array(
                    'key'   => 'arival-date-time',
                    'label' => 'ARIVAL DETAILS',
                    'value' => $passDetails[6]
                )
            ),
            'headerFields' => array(
                array(
                    'key'   => 'trainNum',
                    'label' => 'Train',
                    'value' => $passDetails[1]
                )
            ),
            'backFields' => array(
                array(
                    'key'   => 'passenger-name',
                    'label' => 'Passenger',
                    'value' => $passDetails[2]
                )
            )
        )
    );
    $visualAppearanceKeys = array(
        'backgroundColor' => 'rgb(60, 65, 76)',
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
    $pass->addFile('./_extras/PKPass/images/icon.png');
    $pass->addFile('./_extras/PKPass/images/icon@2x.png');
    $pass->addFile('./_extras/PKPass/images/logo.png');
    $pass->addFile($passDetails[0]);

    $pass->create(true, $ourPassID, $i_passSet);
}