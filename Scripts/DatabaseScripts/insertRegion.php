<?php
include 'databaseFunctions.php';

$lineNum = 1;
$file = fopen('data/regions.txt', 'r') or die('Unable to open file!');
while ($region = fgets($file)) {

	//Check that the region name is in the string length constraint.
	if (!is_string($region) || strlen($region) > 255) {
	   echo "Region name is too long. (Line: ".$lineNum.")".PHP_EOL;
	   $lineNum += 1;
	   continue;
	}

	
	//See if the region is already in the database
	$answer = doesRegionExist($region);
	if ($answer != "doesRegionExist: Region does not exist.") {
	   echo $answer." (Line: ".$lineNum.")".PHP_EOL;
	   $lineNum += 1;
	   continue;
	}


	//Insert the region into the database.
	$answer = insertRegion($region);
	if ($answer !=  "insertRegion: Insertion success.") {
	   echo $answer." (Line: ".$lineNum.")".PHP_EOL;
	   $lineNum += 1;
	   continue;
	}
	
	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum++;
}


fclose($file);
?>
