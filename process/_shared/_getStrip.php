<?php

function getStrip($pass) {
    require_once '_extras/postmark-inbound-php-master/lib/Postmark/Autoloader.php';
	\Postmark\Autoloader::register();

	// this file should be the target of the callback you set in your postmark account
	$inbound = new \Postmark\Inbound(file_get_contents('../_passes/' . $pass . 'posthook.json'));

	$barcodes = array();
	$passCount = 0;

	foreach($inbound->Attachments() as $attachment) {

	    if(strpos($attachment->Name,'jpg') !== false)
	    {
	    	$data = base64_decode($attachment->Content);
	    	file_put_contents('_passes/' . $pass . '/strip-' . $passCount . '.png', $data);
	    	$barcodes = array_push($barcodes, 'strip-' . $passCount . '.png');
	    	$passCount = $passCount + 1;
	    	echo $attachment->Name;
	    }
	}

	return $barcodes;
}
?>