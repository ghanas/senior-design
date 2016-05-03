<?php
include 'databaseFunctions.php';

$lineNum = 1;
$lastPoint = null;


$trailName = "";
$trailLength = 0;
$elevationGain = 0;


$file = fopen($argv[1], 'r') or die('Unable to open file!');
while ($line = fgets($file)) {

	//Parse a line from the file
	$insertVals = explode(',', $line);
	if ($insertVals == false) {
		echo "Line ".$lineNum." is not a comma seperated string.".PHP_EOL;
		exit();
	}
	if (count($insertVals) != 3) {
		echo "Line ".$lineNum." has improper number of arguments.".PHP_EOL;
		exit();
	}


	if ($lineNum == 1) {
		//I don't do a check on the data because the query will
		//fail if the info is invalid anyways.

		$trailName = $insertVals[0];
		$trailLength = round($insertVals[1], 1);
		$elevationGain = round($insertVals[2], 2);

		//Make sure the trail exists
		$trailId = getTrailId($trailName, $trailLength, $elevationGain);
		if (!is_numeric($trailId)) {
			echo "At line ".$lineNum." ".$trailId.PHP_EOL;
			exit();
		}
		$lineNum += 1;
		continue;
	}


	//Expecting this format:
	//lat,long,elev	

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

	$lat = round($insertVals[0], 5);
	$long = round($insertVals[1], 5);
	$elev = round($insertVals[2], 2);


	//See if point exists or not. It's okay for a point to 
	//already exist as long as we use its id.

	$pointId = getPointId($lat, $long, $elev);
	if ($pointId == "getPointId: Query failed." || $pointId == "getPointId: Could not connect to database.") {
		echo "At line ".$lineNum." ".$pointId.PHP_EOL;
		exit();
	}
	else if ($pointId == "getPointId: Point does not exist.") {
		//Try to insert point
		$pointId = insertPoint($lat, $long, $elev);
		if (!is_numeric($pointId)) {
			echo "At line ".$lineNum." ".$pointId.PHP_EOL;
			exit();
		}
	}
	//else - we have the point id already


	//See if the trail point already exists - meaning this point is already on this trail.
	//If so, we have a problem.

	$result = doesTrailPointExist($pointId, $trailId);
	if ($result != "doesTrailPointExist: Trail point does not exist.") {
		echo "At line ".$lineNum." ".$result.PHP_EOL;
		exit();
	}

	
	//Finally, insert the trail point and get the id to
	//create the linked list
	$lastPoint = insertTrailPoint($trailId, $pointId, $lastPoint);
	if (!is_numeric($lastPoint)) {
		echo "At line ".$lineNum." ".$lastPoint.PHP_EOL;
		exit();
	} 

	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum += 1;
}


fclose($file);
?>
