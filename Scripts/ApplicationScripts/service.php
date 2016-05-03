<?php
    
    //Prevent functions from being called outside the application
    if (isset($_POST['request'])) {
        if ($_POST['tokenId'] != '9J39djsi39n9d3j2dKDJ3GJGH55553CMK30DG4FEKkd92'){
            echo 'HACKER ALERT!';
            break;
				}


				//Switch on what function is being called
        switch ($_POST['request']) {
					case 'getTrailPoints':
						getTrailPoints();
						break;
					case 'getFilters':
						getFilters();
						break;
					case 'getParkingLots':
						getParkingLots();
						break;
					case 'getFilteredTrailIds':
						getFilteredTrailIds();
						break;
					case 'getTrailPointsForTrailId':
						getTrailPointsForTrailId();
						break;
					case 'getTrailInfo':
						getTrailInfo();
						break;
					case 'getTrailBuilderInfo':
						getTrailBuilderInfo();
						break;
					case 'getListView':
						getListView();
						break;
					default:
						echo 'Action not defined';
						break;
        }
    }
   


		//Connect to the pre-defined database
		function db_connect() {
			$dbc = new PDO('mysql:host=dbs.eecs.utk.edu;dbname=ghanas', 
				'ghanas', 'applePie1');
			if ($dbc) {
				return $dbc;
			}
			else {
				return false;
			}
		}			

    function getFilteredTrailIds()
    {
			//Connect to the DB
			$dbc = db_connect();
			if ($dbc == false) {
				echo "Error: Could not connect to the database.";
			}


			//Check the Post value of regions argument.
			$regions = array();			
			if ($_POST['regions'] != "default") {
				$regions = explode(',', $_POST['regions']);
			}
			else {
				$regionsSql = "SELECT regionId FROM Region";
				$regionsResult = $dbc->query($regionsSql);
				if ($regionsResult == false) {
					echo 'Error: regions querey failed';
				}
				if ($regionsResult->rowCount() == 0) {
					echo 'Error: no regions in the database';
				}

				while ($row = $regionsResult->fetch(PDO::FETCH_ASSOC)) {
					array_push($regions, $row['regionId']);
				}
			}

			foreach($regions as $I => $val) 
			{ 
				$regions [$I] = "'" . $val . "'";  
			}
			echo $regions;
			$trailSql = "";
			$interestPoints = null;
			if ($_POST['intPoints'] != "default") {
				$interestPoints = explode(',', $_POST['intPoints']);
				$trailSql = "SELECT DISTINCT T.trailId, T.trailName FROM Trail T JOIN Region R ON T.region = R.regionId LEFT JOIN InterestPointTrailMapping IPTMAP ON T.trailId = IPTMAP.trailId LEFT JOIN InterestPoint IP ON IPTMAP.interestPointId = IP.interestPointId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN ('" . implode(',', $regions) . "') AND T.trailId = IPTMAP.trailId AND IP.typeId IN ('". implode(',', $interestPoints) . "')";
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			else {
				$trailSql = "SELECT T.trailId, T.trailName FROM Trail T JOIN RegionTrailMapping RTM ON T.trailId = RTM.trailId JOIN Region R ON RTM.regionId = R.regionId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN (" .implode(",", $regions). ")";
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			
		
			$trailResult = $dbc->query($trailSql);
			if ($trailResult == false) {
				echo 'Error: Trail querey failed.';
			}

			$trailIds = array();
			while ($row = $trailResult->fetch(PDO::FETCH_ASSOC)){
				array_push($trailIds, $row['trailId']);
			}
			
			echo json_encode($trailIds);
			return;
	}

    function getTrailPoints()
    {
			//Connect to the DB
			$dbc = db_connect();
			if ($dbc == false) {
				echo "Error: Could not connect to the database.";
			}


			//Check the Post value of regions argument.
			$regions = array();			
			if ($_POST['regions'] != "default") {
				$regions = explode(',', $_POST['regions']);
			}
			else {
				$regionsSql = "SELECT regionId FROM Region";
				$regionsResult = $dbc->query($regionsSql);
				if ($regionsResult == false) {
					echo 'Error: regions querey failed';
				}
				if ($regionsResult->rowCount() == 0) {
					echo 'Error: no regions in the database';
				}

				while ($row = $regionsResult->fetch(PDO::FETCH_ASSOC)) {
					array_push($regions, $row['regionId']);
				}
			}

			$trailSql = "";
			$interestPoints = null;
			if ($_POST['intPoints'] != "default") {
				$interestPoints = explode(',', $_POST['intPoints']);
				$trailSql = "SELECT DISTINCT T.trailId, T.trailName, T.trailLength, T.elevationGain From Trail T JOIN RegionTrailMapping RTM ON T.trailId = RTM.trailId JOIN Region R ON RTM.regionId = R.regionId JOIN InterestPointTrailMapping IPTMAP ON T.trailId = IPTMAP.trailId JOIN InterestPoint IP ON IPTMAP.interestPointId = IP.interestPointId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN ('" . implode("','", $regions) . "') AND IP.typeId IN ('". implode("','", $interestPoints) . "')";
				
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			else {
				$trailSql = "SELECT DISTINCT T.trailId, T.trailName, T.trailLength, T.elevationGain FROM Trail T JOIN RegionTrailMapping RTM ON T.trailId = RTM.trailId JOIN Region R ON RTM.regionId = R.regionId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN ('" . implode("','", $regions) . "')";
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			
				$trailResult = $dbc->query($trailSql);
				if ($trailResult == false) {
					echo 'Error: Trail querey failed.';
				}
				if ($trailResult->rowCount() == 0) {
					echo 'Error: No rows returned from trail query';
				}



			$max = $trailResult->rowCount();
			$count = 0;
			while ($row = $trailResult->fetch(PDO::FETCH_ASSOC)){
				$obj = new stdClass();
				$obj->trailName = $row['trailName'];
				$obj->trailId = $row['trailId'];
				$obj->trailLength = $row['trailLength'];
				$obj->elevationGain = $row['elevationGain'];
				$obj->points = array();
				$id = $row['trailId'];
				$sql = "SELECT P.longitudePoint, P.latitudePoint FROM TrailPoint TP JOIN Point P ON P.pointId = TP.pointId WHERE TP.trailId = $obj->trailId";
				$result = $dbc->query($sql);
				while ($row = $result->fetch(PDO::FETCH_ASSOC)){
					$point = new stdClass();
					$point->lat = floatval($row['latitudePoint']);
					$point->lng = floatval($row['longitudePoint']);
					array_push($obj->points, $point);
				}
				echo json_encode($obj);
				$count++;
				if ($count < $max) {
					echo "\n";
				}
			}
			
			return;
    }

		function getTrailPointsForTrailId() {

			//Connect to the DB
			$dbc = db_connect();
			if ($dbc == false) {
				echo "Error: Could not connect to the database.";
			}
		
			$sql = "SELECT trailName, trailId FROM Trail WHERE trailId = {$_POST['trailId']}";
			$trailResult = $dbc->query($sql);
			$obj = new stdClass();
			while ($row = $trailResult->fetch(PDO::FETCH_ASSOC)){
				$obj->trailName = $row['trailName'];
				$obj->trailId = $row['trailId'];
				$obj->points = array();
				$sql = "SELECT P.longitudePoint, P.latitudePoint FROM TrailPoint TP JOIN Point P ON P.pointId = TP.pointId WHERE TP.trailId = $obj->trailId";
				$result = $dbc->query($sql);
				while ($row = $result->fetch(PDO::FETCH_ASSOC)){
					$point = new stdClass();
					$point->lat = floatval($row['latitudePoint']);
					$point->lng = floatval($row['longitudePoint']);
					array_push($obj->points, $point);
				}
			}
			echo json_encode($obj);
			return;
		}


		function getFilters() {
			//connect to the DB
			$dbc = db_connect();
			if ($dbc == false) {
				echo 'Error: Could not connect to the database.';
			}
		

			//MySQL queries
			$interestPointsSql = 'SELECT description, typeId FROM InterestPointType';
			$regionsSql = 'SELECT regionName, regionId FROM Region';
			$trailLengthSql = 'SELECT MIN(trailLength), MAX(trailLength) FROM Trail;';
			$trailElevationSql = 'SELECT MIN(elevationGain), MAX(elevationGain) FROM Trail;';
	

			//Perform the queries
			$interestPointResult = $dbc->query($interestPointsSql); 
			$regionsResult = $dbc->query($regionsSql);
			$trailLengthResult = $dbc->query($trailLengthSql);
			$trailElevationResult = $dbc->query($trailElevationSql);
      
			
			//Error checking for query success
			if ($interestPointResult == false) {
				echo 'Error: query for interest points returned false';
			}
			if ($interestPointResult->rowCount() == 0) { 
				echo 'Error: query for the interest points returned 0 rows';
			}
			if($regionsResult == false) {
				echo 'Error: query for the regions returned false';
			}
			if ($regionsResult->rowCount() == 0) {
				echo 'Error: query for the regions returned 0 rows';
			}
			if($trailLengthResult == false) {
				echo 'Error: query for the lengths returned false';
			}
			if ($trailLengthResult->rowCount() != 1) {
				echo 'Error: query for the lengths returned invalid number of rows';
			}
			if($trailElevationResult == false) {
				echo 'Error: query for elevation gains returned false';
			}
			if ($trailElevationResult->rowCount() != 1) {
				echo 'Error: query for elevation gains returned invalid number of rows';
			}


			//Encode the query results in JSON format
			$filters = new stdClass();
			$filters-> regions = array();
			$filters-> interestPoints = array();
			while($row = $trailLengthResult->fetch(PDO::FETCH_ASSOC)){
				$filters-> minTrailLen = $row['MIN(trailLength)'];
				$filters-> maxTrailLen = $row['MAX(trailLength)'];
			}

			while($row = $trailElevationResult->fetch(PDO::FETCH_ASSOC)){
				$filters-> minElevationGain = $row['MIN(elevationGain)'];
				$filters-> maxElevationGain = $row['MAX(elevationGain)'];
			}

			$interestPoints = array();
			while ($row = $interestPointResult->fetch(PDO::FETCH_ASSOC)){
				$filters ->interestPoints[$row['typeId']] = $row['description'];
			}

			while ($row = $regionsResult->fetch(PDO::FETCH_ASSOC)){
				$filters->regions[$row['regionId']] = $row['regionName'];
			}

			echo json_encode($filters);
			return;
		}




		function getTrailInfo() {

			$noData = false;
			$dbc = db_connect();
			if ($dbc == false) {
				echo "Couldn't connect to database";
			}
			$sql = "SELECT trailName, trailLength, trailDifficulty, elevationGain, horseAccessible, trailDescription, isLoop FROM Trail WHERE trailId = {$_POST['trailId']}";
			$trailResult = $dbc->query($sql);
			if ($trailResult == false) {
				echo "Query failed";
			}
			if ($trailResult->rowCount() == 0) {
				echo "no rows returned";
				$noData = true;
			}


			$sql = "SELECT interestPointName, latitudePoint, longitudePoint, elevationPoint, description FROM InterestPoint I JOIN InterestPointTrailMapping IP ON IP.interestPointId = I.interestPointId JOIN Trail T ON T.trailId = IP.trailId LEFT JOIN Point P ON P.pointId = I.pointId JOIN InterestPointType IPT ON IPT.typeId = I.typeId WHERE T.trailId = {$_POST['trailId']}";
			//$sql = "SELECT IP.interestPointName, IPT.description, P.latitudePoint, P.longitudePoint, P.elevationPoint FROM Trail T, InterestPoint IP JOIN InterestPointType IPT ON IP.typeId = IPT.typeId JOIN InterestPointTrailMapping IPTM ON IP.interestPointId = IPTM.interestPointId JOIN Point P ON P.pointId = IP.pointId WHERE T.trailId = {$_POST['trailId']} AND T.trailId = IPTM.trailId";
			$interestPointResult = $dbc->query($sql);
			if ($interestPointResult == false) {
				echo "Interest Point garbage";
				echo "Query failed";
			}
			


			/*$sql = "SELECT P.latitudePoint, P.longitudePoint, P.elevationPoint FROM TrailPoint TP JOIN Point P ON P.pointId = TP.pointId JOIN Intersection I ON I.trailPointId = TP.trailPointId WHERE TP.trailId = {$_POST['trailId']}"; 
			$intersectionResult = $dbc->query($sql);
			if ($intersectionResult == false) {
				echo "Intersection query failed";
			}

*/

			$sql = "SELECT P.*, TP.previousPoint, TP.trailPointId FROM TrailPoint TP JOIN Point P ON P.pointId = TP.pointId WHERE TP.trailId = {$_POST['trailId']} ORDER BY TP.trailPointId";
			$trailPointResult = $dbc->query($sql);
			if ($trailPointResult == false) {
				echo "Error executing trailPoint query.".PHP_EOL;
			}


			$trail = new StdClass();
			$row = $trailResult->fetch(PDO::FETCH_ASSOC);
			$trail->trailName = $row['trailName'];
			$trail->trailLength = $row['trailLength'];
			$trail->trailDifficulty = $row['trailDifficulty'];
			$trail->elevationGain = $row['elevationGain'];
			$trail->horseAccessible = $row['horseAccessible'];
			$trail->trailDescription = $row['trailDescription'];
			$trail->isLoop = $row['isLoop'];
			$trail->trailPoints = array();
				
			
			//Code to get trail points 
			while ($point = $trailPointResult->fetch(PDO::FETCH_ASSOC)) {
				$trailPoint = new StdClass();
				$trailPoint -> lat = $point['latitudePoint'];
				$trailPoint -> long = $point['longitudePoint'];
				$trailPoint -> prevPoint = $point['previousPoint'];
				$trailPoint -> elev = $point['elevationPoint'];
				$trailPoint -> id = $point['trailPointId'];
				array_push($trail->trailPoints, $trailPoint);
			}

			$trail->interestPoints = array();
			if ($interestPointResult->rowCount() != 0) {
				while ($row = $interestPointResult->fetch(PDO::FETCH_ASSOC)) {
					$point = new StdClass();
					$point -> name = $row['interestPointName'];
					$point -> type = $row['description'];
					$point -> latitudePoint = $row['latitudePoint'];
					$point -> longitudePoint = $row['longitudePoint'];
					$point -> elev = $row['elevationPoint'];
					array_push($trail->interestPoints, $point);
				}
			}
			echo json_encode($trail);			
			return;
		}



function getTrailBuilderInfo() {

	$dbc = db_connect();
	if ($dbc == false) {
		echo "Couldn't connect to database";
	}
	$trailSQL = "SELECT trailId, trailName, elevationGain, trailLength FROM Trail";
	$trailResult = $dbc->query($trailSQL);
	if ($trailResult == false) {
		echo "Error executing trailPoint query.".PHP_EOL;
	}

	$trailClass = new StdClass();
	$trailClass->trails = array();
	while ($row = $trailResult->fetch(PDO::FETCH_ASSOC)) {
		$trail = new StdClass();
		$trail->trailName = $row['trailName'];
		$trail->trailLength = $row['trailLength'];
		$trail->elevationGain = $row['elevationGain'];
		$trail->trailId = $row['trailId'];
		$trail->trailPoints = array();
		$trail->intersectionPoints = array();

		//get all trailPoints for the trail
		$sql = "SELECT P.*, TP.previousPoint, TP.trailPointId FROM TrailPoint TP JOIN Point P ON P.pointId = TP.pointId WHERE TP.trailId = {$row['trailId']} ORDER BY TP.trailPointId";
		$trailPointResult = $dbc->query($sql);
		if ($trailPointResult == false) {
			echo "Error executing trailPoint query.".PHP_EOL;
		}

		while ($point = $trailPointResult->fetch(PDO::FETCH_ASSOC)) {
			$trailPoint = new StdClass();
			$trailPoint -> lat = $point['latitudePoint'];
			$trailPoint -> long = $point['longitudePoint'];
			$trailPoint -> prevPoint = $point['previousPoint'];
			$trailPoint -> elev = $point['elevationPoint'];
			$trailPoint -> id = $point['trailPointId'];
			array_push($trail->trailPoints, $trailPoint);
		}

		$sql = "select I.intersectionId, P.latitudePoint, P.longitudePoint, P.elevationPoint From Point P JOIN TrailPoint TP ON TP.pointId = P.pointId JOIN Intersection I ON I.trailPointId = TP.trailPointId WHERE TP.trailId = {$row['trailId']}";
		$intersectionResult = $dbc->query($sql);
		if ($intersectionResult == false) {
			echo "intersection failed".PHP_EOL;
			echo $sql;
		}

		while ($intersection = $intersectionResult->fetch(PDO::FETCH_ASSOC)) {
			$intersectionClass = new StdClass();
			$intersectionClass-> lat = $intersection['latitudePoint'];
			$intersectionClass-> long = $intersection['longitudePoint'];
			$intersectionClass-> elev = $intersection['elevationPoint'];
			$intersectionClass-> id = $intersection['intersectionId'];
			$intersectionClass-> ids = array();

			$sql = "SELECT * FROM IntersectionMapping WHERE intersectionId1 = {$intersection['intersectionId']} OR intersectionId2 = {$intersection['intersectionId']}";
			$res = $dbc->query($sql);
			if ($res == false) {
				echo "map failed".PHP_EOL;
			}
			
			while ($map = $res->fetch(PDO::FETCH_ASSOC)) {
				if ($map['intersectionId1'] == $intersection['intersectionId']) {
					array_push($intersectionClass->ids, $map['intersectionId2']);
				}
				else {
					array_push($intersectionClass->ids, $map['intersectionId1']);
				}
			}
			array_push($trail->intersectionPoints, $intersectionClass);
			
		}//end of intersections


		//Parking lots
		$sql = "SELECT PL.parkingLotId FROM ParkingLot PL JOIN ParkingLotTrailMapping PLTM ON PL.parkingLotId = PLTM.parkingLotId JOIN Trail T ON T.trailId = PLTM.trailId WHERE T.trailId = {$row['trailId']}";
		$res = $dbc->query($sql);
		if ($res == false) {
			echo "parking lot failed";
		}
		
		$trail->lotIds = array();
		while ($lot = $res->fetch(PDO::FETCH_ASSOC)) {
			array_push($trail->lotIds, $lot['parkingLotId']);
		}

		array_push($trailClass->trails, $trail);



	} //end of trail loop
	
	echo json_encode($trailClass);
	return;
}

function getParkingLots() {

	$dbc = db_connect();
	if ($dbc == false) {
		echo "Couldn't connect to database";
	}

	$sql = "SELECT PL.parkingLotId, PL.maxLotCompacity, P.longitudePoint, P.latitudePoint, P.elevationPoint FROM ParkingLot PL JOIN Point P ON P.pointId = PL.pointId";
	$res = $dbc->query($sql);
	if ($res == false) {
		echo "Parking lot failure";
	}

	$parkingLots = array();
	while ($lot = $res->fetch(PDO::FETCH_ASSOC)) {
		$pl = new StdClass();
		$pl->id = $lot['parkingLotId'];
		$pl->cap = $lot['maxLotCapacity'];
		$pl->lat = $lot['latitudePoint'];
		$pl->long = $lot['longitudePoint'];
		$pl->elev = $lot['elevationPoint'];
		array_push($parkingLots, $pl);
	}

	echo json_encode($parkingLots);
	return;

}


		function getListView()
    {
			//Connect to the DB
			$dbc = db_connect();
			if ($dbc == false) {
				echo "Error: Could not connect to the database.";
			}


			//Check the Post value of regions argument.
			$regions = array();			
			if ($_POST['regions'] != "default") {
				$regions = explode(',', $_POST['regions']);
			}
			else {
				$regionsSql = "SELECT regionId FROM Region";
				$regionsResult = $dbc->query($regionsSql);
				if ($regionsResult == false) {
					echo 'Error: regions querey failed';
				}
				if ($regionsResult->rowCount() == 0) {
					echo 'Error: no regions in the database';
				}

				while ($row = $regionsResult->fetch(PDO::FETCH_ASSOC)) {
					array_push($regions, $row['regionId']);
				}
			}

			$trailSql = "";
			$interestPoints = null;
			if ($_POST['intPoints'] != "default") {
				$interestPoints = explode(',', $_POST['intPoints']);
				$trailSql = "SELECT DISTINCT T.trailId, T.trailName, T.trailLength, T.elevationGain From Trail T JOIN RegionTrailMapping RTM ON T.trailId = RTM.trailId JOIN Region R ON RTM.regionId = R.regionId JOIN InterestPointTrailMapping IPTMAP ON T.trailId = IPTMAP.trailId JOIN InterestPoint IP ON IPTMAP.interestPointId = IP.interestPointId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN ('" . implode("','", $regions) . "') AND IP.typeId IN ('". implode("','", $interestPoints) . "')";
				
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			else {
				$trailSql = "SELECT DISTINCT T.trailId, T.trailName, T.trailLength, T.elevationGain FROM Trail T JOIN RegionTrailMapping RTM ON T.trailId = RTM.trailId JOIN Region R ON RTM.regionId = R.regionId WHERE T.trailLength BETWEEN {$_POST['minLen']} AND {$_POST['maxLen']} AND T.elevationGain BETWEEN {$_POST['minElevationGain']} AND {$_POST['maxElevationGain']} AND R.regionId IN ('" . implode("','", $regions) . "')";
				if ($_POST['horse'] != "default") {
					$trailSql = $trailSql . " AND T.horseAccessible = {$_POST['horse']}";
				}
			}
			
				$trailResult = $dbc->query($trailSql);
				if ($trailResult == false) {
					echo 'Error: Trail querey failed.';
				}
				if ($trailResult->rowCount() == 0) {
					echo 'Error: No rows returned from trail query';
				}



			$max = $trailResult->rowCount();
			$count = 0;
			while ($row = $trailResult->fetch(PDO::FETCH_ASSOC)){
				$obj = new stdClass();
				$obj->trailName = $row['trailName'];
				$obj->trailId = $row['trailId'];
				$obj->trailLength = $row['trailLength'];
				$obj->elevationGain = $row['elevationGain'];
				$id = $row['trailId'];
				echo json_encode($obj);
				$count++;
				if ($count < $max) {
					echo "\n";
				}
			}
			
			return;
    }

?>
