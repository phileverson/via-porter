<?php
function getStrips($ourPassID, $postMarkInbound) {
	$barcodes = array();
	$passCount = 0;

	//saving the images in the image to our passes directory
	foreach($postMarkInbound->Attachments() as $attachment) {
	    if(strpos($attachment->Name,'jpg') !== false)
	    {
	    	$data = base64_decode($attachment->Content);
	    	mkdir('_passes/' . $ourPassID . '/' . $passCount);
	    	file_put_contents('_passes/' . $ourPassID . '/' . $passCount . '/strip.png', $data);
	    	$barcodes[$passCount] = 'strip.png';
	    	$passCount = $passCount + 1;
	    }
	}

	return $barcodes;
}
?>