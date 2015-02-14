<?php

function ifLongerSpaceCut($longerString, $maxChar)
{
	if(strlen($longerString) > $maxChar)
	{
		$firstSpace = strpos($longerString, ' ');
		$newShorterWord = substr($longerString, 0, ($firstSpace));
		if(strlen($newShorterWord) > $maxChar)
		{
			return 'too long...';
		}
		return $newShorterWord;
	}
	return $longerString;
}

function getPassDataPrePost($textBody, $preData, $postData, $passOccurence) {
	// echo $passOccurence;
	// if($passOccurence < 1)
	// {
	$boardingPassTextBody = explode('EC NB', $textBody);
	$textBody = $boardingPassTextBody[$passOccurence + 1]; // plus 1 bc there's content above the first thing that we don't want...
	// }
	// if($passOccurence > 0)
	// {
	// 	$boardingPassTextBody = explode('EC NB', $textBody);
	// 	$textBody = $boardingPassTextBody[$passOccurence];
	// 	echo ' running the second one';
	// }

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
	$departureStation = substr($allString, 0, $firstDate);
	$departureStation = ifLongerSpaceCut($departureStation, 18);
	$departureStation = preg_replace('/\s+/', '', $departureStation);
	$toFromData[0] = $departureStation;

	//getting departure date and time
	$departDateNoStop = substr($allString, ($firstDate + 8));
	$departDate = substr($departDateNoStop, 5, 13);

	$departureWord = strpos($departDateNoStop, ' Departure : ');
	$departTime = substr($departDateNoStop, $departureWord + 13, 8);

	$toFromData[1] = $departDate . ' ' . $departTime;

	//getting the arrival station
	$arivalStationNoStop = substr($departDateNoStop, ($departureWord + 21));
	$secondDate = strpos($arivalStationNoStop, 'Date');
	$arrivalStation = substr($arivalStationNoStop, 0, 30);
	$arrivalStation = ifLongerSpaceCut(trim($arrivalStation), 15);
	$arrivalStation = preg_replace('/\s+/', '', $arrivalStation);
	$toFromData[2] = $arrivalStation;

	//getting arrival date and time
	$wordArrival = strpos($arivalStationNoStop, 'Arrival : ');
	$arrivalDate = substr($arivalStationNoStop, $secondDate + 12, 13);
	$arivalTime = substr($arivalStationNoStop, ($wordArrival + 10), 8);
	$toFromData[3] =  $arrivalDate . ' ' . $arivalTime; //arival date and time

	return $toFromData;
}

function viaRailToFromVIABarCode($returnedCode)
{
	$viaCodes = array();

	$wordVIA = strpos($returnedCode, 'VIA');
	$originCode = substr($returnedCode, ($wordVIA - 8), 4);
	$destinationCode = substr($returnedCode, ($wordVIA - 4), 4);

	$viaCodes[0] = $originCode;
	$viaCodes[1] = $destinationCode;

	return $viaCodes;
}



function viaRailSeat($returnedCode)
{
	$viaRailSeat = array();

	$wordVIA = strpos($returnedCode, 'VIA');
	$seat = substr($returnedCode, ($wordVIA - 12), 3);
	$car = substr($returnedCode, ($wordVIA - 16), 5);

	$viaRailSeat[0] = $seat;
	$viaRailSeat[1] = $car;

	return $viaRailSeat;
}




?>