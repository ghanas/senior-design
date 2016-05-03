<?php
include 'databaseFunctions.php';

$lineNum = 1;
$file = fopen('data/trails.csv', 'r') or die('Unable to open file!');
while ($line = fgets($file)) {


//trail name, trail length, elevation gain, horse Accessible, trail description, is loop, list of regions ...	
	

	//Make sure the line is comma seperated values and that 
	//there are at least 8 fields.
	$insertVals = explode(',', $line);
	if ($insertVals == false) {
		echo "Line ".$lineNum." is not a comma seperated string.".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	
	if (count($insertVals) < 7) {
		echo "Line ".$lineNum." has improper number of arguments.".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	//Check all of the fields
	if (!is_string($insertVals[0]) || strlen($insertVals[0]) > 255) {
		echo "Trail name is either not a string, or exceeds 255 characters at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if (!is_numeric($insertVals[1]) || $insertVals[1] > 9999.9 || $insertVals[1] <= 0) {
		echo "Trail length is either not a number, exceeds 9999.9, or is less than or equal to 0 at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if (!is_numeric($insertVals[2]) || $insertVals[2] > 999999.99 || $insertVals[2] <= 0) {
		echo "Trail elevation gain is either not a number, exceeds 999999.99, or is less than 0 at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if (!is_numeric($insertVals[3]) || !($insertVals[3] == 3 || $insertVals[3] == 2 || $insertVals[3] == 1)) {
		echo "Horse Accessibility for the trail is either not a number, or is not one of the predefined valid values (1,2,3) at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if (!is_string($insertVals[4]) || strlen($insertVals[4]) > 3000) {
		echo "Trail description is either not a string, or exceeds 3000 characters at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	if ($insertVals[5] != "true" && $insertVals[5] != "false") {
		echo "Trail's loop field is neither true or false at line ".$lineNum.".".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	
	$bad = false;
	for ($i = 6; $i < count($insertVals); $i++) {
		if (!is_string($insertVals[$i]) || strlen($insertVals[$i]) > 255) {
			echo "Trail region is either not a string or exceeds 255 characters at line ".$lineNum.".".PHP_EOL;
			$bad = true;
		} 
	}
	if ($bad) {
		$lineNum += 1;
		continue;
	}
	


	//If all checks have passed now we go ahead and set variables.
	$trailName = $insertVals[0];
	$trailLength = round($insertVals[1], 1);
	$elevationGain = round($insertVals[2], 2);
	$horseAccessible = $insertVals[3];
	$trailDescription = $insertVals[4];
	$isLoop = 0;
	if ($insertVals[5] == "true") {
		$isLoop = 1;
	}

	$regionNames = array();
	for ($i = 6; $i < count($insertVals); $i++) {
		array_push($regionNames, $insertVals[$i]);
	}



	//Check if trail already exists:
	$result = doesTrailExist($trailName, $trailLength, $elevationGain);
	if ($result != "doesTrailExist: Trail does not exist.") {
		echo "At line ".$lineNum." ".$result.PHP_EOL;
		$lineNum += 1;
		continue;
	} 

	//Check if the regions already exist by getting the region ids:
	$regionIds = array();
	$bad = false;
	for ($i = 0; $i < count($regionNames); $i++) {
		$regionId = getRegionId($regionNames[$i]);
		if (is_numeric($regionId)) {
			array_push($regionIds, $regionId);
		}
		else {
			echo "At line ".$lineNum." ".$regionId.PHP_EOL;
			$bad = true;
			$i = count($regionNames);
			$lineNum += 1;
		}
	}

	if ($bad) {
		continue;
	}



	//Insert trail
	$trailId = insertTrail($trailName, $trailLength, $elevationGain, $horseAccessible, $trailDescription, $isLoop);
	if (!is_numeric($trailId)) {
		echo "At line ".$lineNum." ".$trailId.PHP_EOL;
		$lineNum += 1;
		continue;
	}

	
	//Double check to make sure none of the trail region mappings exist
	$bad = false;
	for ($i = 0; $i < count($regionIds); $i++) {
		$result = doesRegionTrailMappingExist($trailId, $regionIds[$i]);
		if ($result != "doesRegionTrailMappingExist: mapping does not exist.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			deleteTrailAndRegionTrailMapping($trailId, $lineNum);
			$lineNum += 1;
			$i = count($regionIds);
			$bad = true;
		}
	}
	
	if ($bad) {
		continue;
	}


	//Do all the mapping inserts
	$bad = false;
	for ($i = 0; $i < count($regionIds); $i++) {
		$result = insertRegionTrailMapping($regionIds[$i], $trailId);
		if ($result != "insertRegionTrailMapping: Insertion success.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
			deleteTrailAndRegionTrailMapping($trailId, $lineNum);
			$lineNum += 1;
			$i = count($regionIds);
			$bad = true;
		}
	}

	if ($bad) {
		continue;
	}
	
	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum += 1;

}
fclose($file);
?>
