<?php
include 'databaseFunctions.php';

$lineNum = 1;
$file = fopen('data/interestPointTypes.txt', 'r') or die('Unable to open file!');
while ($description = fgets($file)) {
	

	if (!is_string($description) || strlen($description) > 50) {
		echo "Description is either not a string or exceeds 50 characters at line ".$lineNum.PHP_EOL;
		$lineNum += 1;
		continue;
	}

	$result = doesInterestPointTypeExist($description);
	if ($result != "doesInterestPointTypeExist: Interest point type does not exist.") {
		echo "At line ".$lineNum." ".$result.PHP_EOL;
		$lineNum += 1;
		continue;
	}	
	
	$result = insertInterestPointType($description);
	if ($result != "insertInterestPointType: Insertion success.") {
		echo "At line ".$lineNum." ".$result.PHP_EOL;
		$lineNum += 1;
		continue;
	}	

	echo "Line ".$lineNum." was successful.".PHP_EOL;
	$lineNum += 1;
}


fclose($file);
?>
