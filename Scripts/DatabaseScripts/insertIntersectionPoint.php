<?php
include 'databaseFunctions.php';

$lineNum = 1;
$intersectionIds = array();
$file = fopen('data/intersections.csv', 'r') or die('Unable to open file!');
while ($line = fgets($file)) {

	//Parse a line from the file
	$insertVals = explode(',', $line);
	if ($insertVals == false) {
		echo "Line ".$lineNum." is not a comma seperated string.".PHP_EOL;
		$lineNum += 1;
		continue;
	}

	
	if (count($insertVals) != 6) {
		echo "Line ".$lineNum." has improper number of arguments.".PHP_EOL;
		$lineNum += 1;
		continue;
	}


	if ($insertVals[0] == 'map') {
		$result = insertIntersectionMapping($intersectionIds);
		if ($result != "insertIntersectionMapping: Insertion success.") {
			echo "At line ".$lineNum." ".$result.PHP_EOL;
		}
		$lineNum++;
		$intersectionIds = null;
		$intersectionIds = array();
		continue;
		//Do more stuff
	}

	$lat = round($insertVals[0], 5);
	$long = round($insertVals[1], 5);
	$elev = round($insertVals[2], 2);
	$name = $insertVals[3];
	$len = round($insertVals[4], 1);
	$elevGain = round($insertVals[5], 2);
	

	$trailPointId = getTrailPointId($lat, $long, $elev, $name, $len, $elevGain);
	if (!is_numeric($trailPointId)) {
		echo "At line ".$lineNum." ".$trailPointId.PHP_EOL;
		$lineNum += 1;
		continue;
	}


	//Does intersection already exist?
	$pre = true;
	$intersectionId = getIntersectionId($trailPointId);
	if (!is_numeric($intersectionId)) {
		$pre = false;
		$intersectionId = insertIntersection($trailPointId);
		if (!is_numeric($intersectionId)) {
			echo "At line ".$lineNum." ".$intersectionId.PHP_EOL;
			$lineNum += 1;
			continue;
		}
	}

	array_push($intersectionIds, $intersectionId);
	if ($pre) {
		echo "Line ".$lineNum." was successful (Point already existed)".PHP_EOL;
	}
	else {
		echo "Line ".$lineNum." was successful.".PHP_EOL;
	}
	$lineNum += 1;
}

fclose($file);
?>
