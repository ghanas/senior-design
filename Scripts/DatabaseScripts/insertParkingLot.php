<?php
include 'databaseFunctions.php';

$lineNum = 1;
$file = fopen('data/parkingLots.csv', 'r') or die('Unable to open file!');
while ($line = fgets($file)) {
	
	//Parse a line of the file
	$insertVals = explode(',', $line);
	if ($insertVals == false) {
		echo "Line ".$lineNum." is not a comma seperated string.".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	
	if (count($insertVals) < 6) {
		echo "Line ".$lineNum." has improper number of arguments.".PHP_EOL;
		$lineNum += 1;
		continue;
	}



	//lat, long, elev, trail name, trail length, trail elevation gain
	if (!is_numeric($insertVals[0]) || $insertVals[0] > 90 || $insertVals[0] < -90) {
		echo "Latitude is either not a number, exceeds 90, or is less than -90 at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		exit();
	}
	
	if (!is_numeric($insertVals[1]) || $insertVals[1] > 180 || $insertVals[1] < -180) {
		echo "Longitude is either not a number, exceeds 180, or is less than -180 at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		exit();
	}

	if (!is_numeric(trim($insertVals[2])) || $insertVals[2] > 999999.99 || $insertVals[2] <= 0) {
		echo "Elevation is either not a number, exceeds 999999.99, or is less than 0 at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		exit();
	}

	//Don't need to check trail info, because I assume it is accurate (trail already exists)


	$lat = round($insertVals[0], 5);
	$long = round($insertVals[1], 5);
	$elev = round($insertVals[2], 2);
	$trailName = $insertVals[3];
	$trailLength = round($insertVals[4], 1);
	$elevGain = round($insertVals[5], 2);


	//See if trail exists
	$trailId = getTrailId($trailName, $trailLength, $elevGain);
	if (!is_numeric($trailId)) {
		echo "At line ".$lineNum." ".$trailId.PHP_EOL;
		$lineNum += 1;
		continue;
	}


	//See if point exists - it can since multiple trails can have the same lot
	//If not, insert it
	$pointId = getPointId($lat, $long, $elev);
	if (!is_numeric($pointId)) { //Does not exist - insert
		$pointId = insertPoint($lat, $long, $elev);
		if (!is_numeric($pointId)) {
			echo "At line ".$lineNum." ".$pointId.PHP_EOL;
			$lineNum += 1;
			continue;
		}
	}
	
	//See if parking lot already exists - which is fine
	//If not, insert it
	$lotId = getParkingLotId($pointId);
	if (!is_numeric($lotId)) {

		$lotId = insertParkingLot($pointId);
		if (!is_numeric($lotId)) {
			echo "At line ".$lineNum." ".$lotId.PHP_EOL;
			$lineNum += 1;
			continue;
		}
	}
	
	//Now to insert the mapping
	//Make sure the mapping doesn't exist
	$result = doesParkingLotTrailMappingExist($lotId, $trailId);
	if ($result != "doesParkingLotTrailMappingExist: mapping does not exist.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			$lineNum += 1;
			continue;
	}

	$result = insertParkingLotTrailMapping($lotId, $trailId);
	if ($result != "insertParkingLotTrailMapping: Insertion success.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			$lineNum += 1;
			continue;
	}
	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum += 1;

}

fclose($file);
?>
