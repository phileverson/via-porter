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
	$departDateNoStop = substr($allString, ($firstDate + 8));
	$departDate = substr($departDateNoStop, 5, 13);

	$departureWord = strpos($departDateNoStop, ' Departure : ');
	$departTime = substr($departDateNoStop, $departureWord + 13, 8);

	$toFromData[1] = $departDate . ' ' . $departTime;

	//getting the arrival station
	$arivalStationNoStop = substr($departDateNoStop, ($departureWord + 21));
	$secondDate = strpos($arivalStationNoStop, ' Date : ');
	$toFromData[2] = substr($arivalStationNoStop, 0, $secondDate); //arrival station

	//getting arrival date and time
	$wordArrival = strpos($arivalStationNoStop, 'Arrival : ');
	$arrivalDate = substr($arivalStationNoStop, $secondDate + 12, 13);
	$arivalTime = substr($arivalStationNoStop, ($wordArrival + 10), 8);
	$toFromData[3] =  $arrivalDate . ' ' . $arivalTime; //arival date and time

	return $toFromData;
}

?>