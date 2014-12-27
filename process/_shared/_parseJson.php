<?php


function getPassDataPrePost($textBody, $preData, $postData, $passOccurence) {
	$boardingPassTextBody = explode('REFUND/EXCHANGE', $textBody);
	$textBody = $boardingPassTextBody[$passOccurence];

	$posPreData = strpos($textBody, $preData) + strlen($preData);
	$posPostData = strpos($textBody, $postData);
	$objLength = $posPostData - $posPreData;
	$objWeWant = substr($textBody, $posPreData, $objLength);
	return $objWeWant;
}

function getPassDataPost($stringPassed, $postData) {
	$posPostData = strpos($stringPassed, $postData);
	$objWeWant = substr($stringPassed, 0, $posPostData);
	return $objWeWant;
}

?>