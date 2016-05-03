<?php
include 'databaseFunctions.php';

$lineNum = 1;
$file = fopen('data/interestPoints.csv', 'r') or die('Unable to open file!');
while ($line = fgets($file)) {

	//Parse a line from the file
	$insertVals = explode(",", $line);
	if ($insertVals == false) {
		echo "Line ".$lineNum." is not a comma seperated string.".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if (count($insertVals) != 8) {
		echo "Line ".$lineNum." has improper number of arguments.".PHP_EOL;
		$lineNum += 1;
		continue;
	}
	
	// name, type, lat, long, elv, trailname, trail length, trail, elevation gain
	if (!is_string($insertVals[0]) || strlen($insertVals[0]) > 255) {
		echo "Interest point name is either not a string, or exceeds 255 characters at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}


	$IPName = $insertVals[0];
	$type = $insertVals[1];

	if ($insertVals[2] == 'none' || $insertVals[3] == 'none' || $insertVals[4] == 'none') {
		$lat = null;
		$long = null;
		$elev = null;
	}
	else {
		$lat = round($insertVals[2], 5);
		$long = round($insertVals[3], 5);
		$elev = round($insertVals[4], 2);
	}

	$trailName = $insertVals[5];
	$trailLength = round($insertVals[6], 1);
	$elevationGain = round($insertVals[7], 2);




	//Make sure trail already exists in the database and get its id
	$trailId = getTrailId($trailName, $trailLength, $elevationGain);
	if (!is_numeric($trailId)) {
		echo "trail";
		echo "At line ".$lineNum." ".$trailId.PHP_EOL;
		$lineNum += 1;
		continue;
	}


	//Make sure the interest point type already exists in the database
	$typeId = getInterestPointTypeId($type);
	if (!is_numeric($typeId)) {
		echo "At line ".$lineNum." ".$typeId.PHP_EOL;
		$lineNum += 1;
		continue;
	}		


	//Check the lat/long/elev fields
	//if any of them are strings, they should all say "none" meaning
	//the interest point spans the whole trail (aka forest)
	$pointId = null;
	if ($lat != null && $long != null && $elev != null) {
		if (!is_numeric($lat) || $lat > 90 || $lat < -90) {
			echo "Latitude is either not a number, exceeds 90, or is less than -90 at line ".$lineNum.".".PHP_EOL;
			$lineNum += 1;
			continue;
		}
	
		if (!is_numeric($long) || $long > 180 || $long < -180) {
			echo "Longitude is either not a number, exceeds 180, or is less than -180 at line ".$lineNum.".".PHP_EOL;
			$lineNum += 1;
			continue;
		}

		if (!is_numeric(trim($elev)) || $elev > 999999.99 || $elev < 0) {
			echo "Elevation is either not a number, exceeds 999999.99, or is less than 0 at line ".$lineNum.".".PHP_EOL;
			$lineNum += 1;
			continue;
		}
	}

		
		//See if this is already a point on the trail, if not, insert it.
	if ($lat != null && $long != null && $elev != null) {
		$pointId = getPointId($lat, $long, $elev);
		if (!is_numeric($pointId) && $pointId != "getPointId: Query failed.") {
			$pointId = insertPoint($lat, $long, $elev);
		
			if (!is_numeric($pointId)) {
				echo "At line ".$lineNum." ".$pointId.PHP_EOL;
				$lineNum += 1;
				continue;
			}
		}
	}


	//See if interest point already exists
	//if it does, that is okay, go on to the mapping
	//if it doesn't, insert it
	$IPId = getInterestPointId($pointId, $IPName, $typeId);
	if (!is_numeric($IPId)) { 
		$IPId = insertInterestPoint($pointId, $IPName, $typeId);
		
		if (!is_numeric($IPId)) {
			echo "At line ".$lineNum." ".$IPId.PHP_EOL;
			$lineNum += 1;
			continue;
		}
	}

	//See if trail interest point mapping exists
	$result = doesInterestPointTrailMappingExist($IPId, $trailId);
	if ($result != "doesInterestPointTrailMappingExist: mapping does not exist.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			$lineNum += 1;
			continue;
	}

	$result = insertInterestPointTrailMapping($IPId, $trailId);
	if ($result != "insertInterestPointTrailMapping: Insertion success.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			$lineNum += 1;
			continue;	
	}

	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum += 1;
}


fclose($file);
?>
