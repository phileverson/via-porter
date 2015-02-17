<?php
function getStrips($ourPassID, $postMarkInbound) {
	$barcodes = array();
	$barcodesNames = array();
	$passCount = 0;

	//saving the images in the image to our passes directory
	foreach($postMarkInbound->Attachments() as $attachment) {
	    if(strpos($attachment->Name,'jpg') !== false)
	    {
	    	if((strlen($attachment->Name) == 29) || (strlen($attachment->Name) == 30))
	    	{
	    		if(checkCurrentBars($barcodesNames, $attachment->Name))
	    		{
		    		$barcodesNames[$passCount] = $attachment->Name;
			    	$data = base64_decode($attachment->Content);
			    	mkdir('_passes/' . $ourPassID . '/' . $passCount);
			    	file_put_contents('_passes/' . $ourPassID . '/' . $passCount . '/strip.png', $data);
			    	$barcodes[$passCount] = 'strip.png';
			    	$passCount = $passCount + 1;
		    	}
	    	}
	    }
	}
	return $barcodes;
}

function checkCurrentBars($barcodesA, $maybeName)
{
	for ($i=0; $i < count($barcodesA) ; $i++) { 
		if($barcodesA[$i] == $maybeName)
		{
			return false;
		}
	}
	return true;
}
?>