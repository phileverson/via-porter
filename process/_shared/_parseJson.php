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

function viaRailToFrom($allString) {
	$toFromData = array(); //array we'll return with the stuff in it
	$firstSpace = strpos($allString, ' ') + 1; 
	$allString = substr($allString, $firstSpace);

	//getting the departure station
	$firstDate = strpos($allString, ' Date : ');
	$toFromData[0] = substr($allString, 0, $firstDate); //departure station

	//getting departure date and time
	$firstDepartureWord = strpos($allString, ' Departure : ');
	$departDate = substr($allString, ($firstDate + 8), $firstDepartureWord);
	$departTime = substr($allString, ($firstDepartureWord + strlen($firstDepartureWord)), $($firstDepartureWord + strlen($firstDepartureWord)) + 8);
	$toFromData[1] = $departDate . ' ' . $departTime;

	return $toFromData;
}

?>