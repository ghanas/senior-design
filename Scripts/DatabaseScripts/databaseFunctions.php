<?php

//Connect to the predefined database
function dbConnect() {
	//$dbc = new PDO('mysql:host=dbs.eecs.utk.edu;dbname=ghanas', 
	//		'ghanas', 'applePie1');
	$dbc = new PDO('mysql:host=127.0.0.1;dbname=art_of_hiking', 'root', 'Xblade32');
	if ($dbc) {
		return $dbc;
	}
	else {
		return false;
	}
}


function doesRegionExist($name) {

	 $return = "doesRegionExist: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "SELECT regionId FROM Region WHERE regionName = $name";
	    $result = $dbc->query($sql);
	    if ($result == false) {
	       $return = "doesRegionExist: Querey failed."; //Query failed
	    }
	    
	    if ($result->rowCount() == 0) {
	       $return = "doesRegionExist: Region does not exist."; //Not in database
	    }
	    else {
	    	 $return = "doesRegionExist: Region exists."; //Success!
	    }	 
	 }
	 
	 $dbc = false;
	 return $return;
}


function insertRegion($name) {

	 $return = "insertRegion: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO Region(regionName) VALUES ($name)";
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
	       $return = "insertRegion: Query failed.";
	    }
	    else {
	       $return = "insertRegion: Insertion success.";
	    }
	 }

	 $dbc = false;
	 return $return;
}


function getRegionId($name) {

	$return = "getRegionId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT regionId FROM Region WHERE regionName = $name";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getRegionId: Query failed.";
		}
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['regionId'];
		}
	}

	$dbc = false;
	return $return;
}


function doesTrailExist($name, $length, $elevation) {
	$return = "doesTrailExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT trailId FROM Trail WHERE trailName = $name AND trailLength = $length AND elevationGain = $elevation";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesTrailExist: Query failed.";
		} 
		else if ($result->rowCount() == 0) {
			$return = "doesTrailExist: Trail does not exist.";
		}
		else {
			$return = "doesTrailExist: Trail exists.";
		}
	}

	$dbc = false;
	return $return;
}


function getTrailId($name, $length, $elevation) {

	$return = "getTrailId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT trailId FROM Trail WHERE trailName = $name AND trailLength = $length AND elevationGain = $elevation";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getTrailId: Query failed.";
			echo $sql.PHP_EOL;
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['trailId'];
		}
	}

	$dbc = false;
	return $return;
}


function insertTrail($name, $length, $elevation, $horse, $description, $loop) {

	 $return = "insertTrail: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO Trail(trailName, trailLength, elevationGain, horseAccessible, trailDescription, isLoop) VALUES ($name, $length, $elevation, $horse, $description, $loop)";
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
	       $return = "insertTrail: Query failed.";
	    }
	    else {
	       $return = $dbc->lastInsertId();;
	    }
	 }

	 $dbc = false;
	 return $return;
}



function doesRegionTrailMappingExist($trailId, $regionId) {

	$return = "doesRegionTrailMappingExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT * FROM RegionTrailMapping WHERE regionId = $regionId AND trailId = $trailId";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesRegionTrailMappingExist: Query failed";
		}
		else if ($result->rowCount() == 0) {
			$return = "doesRegionTrailMappingExist: mapping does not exist.";
		}
		else {
			$return = "doesRegionTrailMappingExist: mapping does exist.";
		}
	}

	$dbc = false;
	return $return;
}


function deleteTrail($id) {

	$return = "deleteTrail: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "DELETE FROM Trail WHERE trailId = $id";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "deleteTrail: Query failed.";
		}
		else if ($result->rowCount() == 0) {
			$return = "deleteTrail: No trail to be deleted.";
		}
		else {
			$return = "deleteTrail: Trail deleted.";
		}
	}

	$dbc = false;
	return $return;
}


function deleteRegionTrailMapping($id) {

	$return = "deleteRegionTrailMapping: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "DELETE FROM RegionTrailMapping WHERE trailId = $id";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "deleteRegionTrailMapping: Query failed.";
		}
		else if ($result->rowCount() == 0) {
			$return = "deleteRegionTrailMapping: No mapping to be deleted.";
		}
		else {
			$return = "deleteRegionTrailMapping: Mapping deleted.";
		}
	}

	$dbc = false;
	return $return;
}

function deleteTrailAndRegionTrailMapping($id, $lineNum) {
		
	$trailDel = deleteTrail($id);
	$mapDel = deleteRegionTrailMapping($id);

	if ($trailDel != "deleteTrail: Trail deleted." && $mapDel != "deleteRegionTrailMapping: Mapping deleted.") {
		echo "At line ".$lineNum." :".PHP_EOL;
		echo "\t".$trailDel.PHP_EOL;
		echo "\t".$mapDel.PHP_EOL;
	}

	return;
}


function insertRegionTrailMapping($regionId, $trailId) {

	$return = "insertRegionTrailMapping: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "INSERT INTO RegionTrailMapping(regionId, trailId) VALUES($regionId, $trailId)";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			if ($result == false) {
			}
			$return = "insertRegionTrailMapping: Query failed.";
		}
		else {
			$return = "insertRegionTrailMapping: Insertion success.";
		}
	}

	$dbc = false;
	return $return;
}



function getPointId($lat, $long, $elev) {

	$return = "getPointId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT pointId FROM Point WHERE latitudePoint = $lat AND longitudePoint = $long AND elevationPoint = $elev"; 
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "getPointId: Query failed.";
		} 
		else if ($result->rowCount() != 1) {
			$return = "getPointId: Point does not exist.";
		}
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['pointId'];
		}
	}

	$dbc = false;
	return $return;
}




function insertPoint($lat, $long, $elev) {

	 $return = "insertPoint: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO Point(latitudePoint, longitudePoint, elevationPoint) VALUES ($lat, $long, $elev)";
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
				$return = "insertPoint: Query failed.";
	    }
	    else {
	       $return = $dbc->lastInsertId();
	    }
	 }

	 $dbc = false;
	 return $return;
}



function doesTrailPointExist($pointId, $trailId) {
	$return = "doesTrailPointExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT trailPointId FROM TrailPoint WHERE trailId = $trailId AND pointId = $pointId"; 
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesTrailPointExist: Query failed.";
		} 
		else if ($result->rowCount() == 0) {
			$return = "doesTrailPointExist: Trail point does not exist.";
		}
		else {
			$return = "doesTrailPointExist: Trail point exists.";
		}
	}

	$dbc = false;
	return $return;
}



function insertTrailPoint($trailId, $pointId, $prev) {

	 $return = "insertTrailPoint: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO TrailPoint(trailId, pointId, previousPoint) VALUES ($trailId, $pointId, $prev)";
			if ($prev == null) {
	    	$sql = "INSERT INTO TrailPoint(trailId, pointId) VALUES ($trailId, $pointId)";
			}
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
	       $return = "insertTrailPoint: Query failed.";
	    }
	    else {
	       $return = $dbc->lastInsertId();
	    }
	 }

	 $dbc = false;
	 return $return;
}


function doesInterestPointTypeExist($desc) {
	$return = "doesInterestPointTypeExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT typeId FROM InterestPointType WHERE description = $desc";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesInterestPointTypeExist: Query failed.";
		} 
		else if ($result->rowCount() == 0) {
			$return = "doesInterestPointTypeExist: Interest point type does not exist.";
		}
		else {
			$return = "doesInterestPointTypeExist: Interest point type exists.";
		}
	}

	$dbc = false;
	return $return;
}


function insertInterestPointType($desc) {

	 $return = "insertInterestPointType: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO InterestPointType(description) VALUES ($desc)";
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
	       $return = "insertInterestPointType: Query failed.";
	    }
	    else {
	       $return = $dbc->lastInsertId(); 
	    }
	 }

	 $dbc = false;
	 return $return;
}



function getInterestPointTypeId($desc) {

	$return = "getInterestPointTypeId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT typeId FROM InterestPointType WHERE description = $desc";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getInterestPointTypeId: Query failed.";
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['typeId'];
		}
	}

	$dbc = false;
	return $return;
}

function getInterestPointId($pointId, $name, $typeId) {

	$return = "getInterestPointId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		
		$sql = "SELECT interestPointId FROM InterestPoint WHERE pointId is NULL AND interestPointName = $name AND typeId = $typeId";
		if ($pointId != NULL) {
			$sql = "SELECT interestPointId FROM InterestPoint WHERE pointId = $pointId AND interestPointName = $name AND typeId = $typeId";
		}


		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getInterestPointId: Query failed.";
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['interestPointId'];
		}
	}

	$dbc = false;
	return $return;
}



function insertInterestPoint($pointId, $name, $typeId) {

	 $return = "insertInterestPoint: Could not connect to database.";
	 $dbc = dbConnect();
	 if ($dbc != false) {
	    $sql = "INSERT INTO InterestPoint(pointId, interestPointName, typeId) VALUES ($pointId, $name, $typeId)";
			if ($pointId == NULL) {
	    	$sql = "INSERT INTO InterestPoint(interestPointName, typeId) VALUES ($name, $typeId)";
			}
	    $result = $dbc->query($sql);
	    if ($result == false || $result->rowCount() != 1) {
	       $return = "insertInterestPoint: Query failed.";
	    }
	    else {
	       $return = $dbc->lastInsertId();
	    }
	 }

	 $dbc = false;
	 return $return;
}


function doesInterestPointTrailMappingExist($IPId, $trailId) {

	$return = "doesInterestPointTrailMappingExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT * FROM InterestPointTrailMapping WHERE interestPointId = $IPId AND trailId = $trailId";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesInterestPointTrailMappingExist: Query failed";
			echo $IPId." : ".$trailId.PHP_EOL;
		}
		else if ($result->rowCount() == 0) {
			$return = "doesInterestPointTrailMappingExist: mapping does not exist.";
		}
		else {
			$return = "doesInterestPointTrailMappingExist: mapping does exist.";
		}
	}

	$dbc = false;
	return $return;
}

function insertInterestPointTrailMapping($IPId, $trailId) {

	$return = "insertInterestPointTrailMapping: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "INSERT INTO InterestPointTrailMapping(interestPointId, trailId) VALUES($IPId, $trailId)";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			if ($result == false) {
			}
			$return = "insertInterestPointTrailMapping: Query failed.";
		}
		else {
			$return = "insertInterestPointTrailMapping: Insertion success.";
		}
	}

	$dbc = false;
	return $return;
}

function getParkingLotId($pointId) {

	$return = "getParkingLotId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT parkingLotId FROM ParkingLot WHERE pointId = $pointId";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getParkingLotId: Query failed.";
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['parkingLotId'];
		}
	}

	$dbc = false;
	return $return;
}



function insertParkingLot($pointId) {

	$return = "insertParkingLot: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "INSERT INTO ParkingLot(pointId) VALUES($pointId)";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			if ($result == false) {
			}
			$return = "insertParkingLot: Query failed.";
		}
		else {
	    $return = $dbc->lastInsertId();
		}
	}

	$dbc = false;
	return $return;
}

function doesParkingLotTrailMappingExist($lotId, $trailId) {

	$return = "doesParkingLotTrailMappingExist: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT * FROM ParkingLotTrailMapping WHERE parkingLotId = $lotId AND trailId = $trailId";
		$result = $dbc->query($sql);
		if ($result == false) {
			$return = "doesParkingLotTrailMappingExist: Query failed";
		}
		else if ($result->rowCount() == 0) {
			$return = "doesParkingLotTrailMappingExist: mapping does not exist.";
		}
		else {
			$return = "doesParkingLotTrailMappingExist: mapping does exist.";
		}
	}

	$dbc = false;
	return $return;
}

function insertParkingLotTrailMapping($lotId, $trailId) {

	$return = "insertParkingLotTrailMapping: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "INSERT INTO ParkingLotTrailMapping(parkingLotId, trailId) VALUES($lotId, $trailId)";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			if ($result == false) {
			}
			$return = "insertInterestPointTrailMapping: Query failed.";
		}
		else {
			$return = "insertParkingLotTrailMapping: Insertion success.";
		}
	}

	$dbc = false;
	return $return;
}



function getTrailPointId($lat, $long, $elev, $name, $len, $elevGain) {

	$return = "getTrailPointId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT trailPointId FROM Trail T JOIN TrailPoint TP ON T.trailId = TP.trailId JOIN Point P ON P.pointId = TP.pointId WHERE P.longitudePoint = $long AND P.latitudePoint = $lat  AND P.elevationPoint = $elev AND T.trailName = $name AND T.trailLength = $len AND T.elevationGain = $elevGain"; 
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getTrailPointId: Query failed.";
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['trailPointId'];
		}
	}

	$dbc = false;
	return $return;
}

/////
function getIntersectionId($trailPointId) {

	$return = "getIntersectionId: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$sql = "SELECT intersectionId FROM Intersection WHERE trailPointId = $trailPointId";
		$result = $dbc->query($sql);
		if ($result == false || $result->rowCount() != 1) {
			$return = "getIntersectionId: Query failed.";
		} 
		else {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$return = $row['intersectionId'];
		}
	}

	$dbc = false;
	return $return;
}

function insertIntersection($trailPointId) {

  $return = "insertIntersection: Could not connect to database.";
  $dbc = dbConnect();
  if ($dbc != false) {
    $sql = "INSERT INTO Intersection(trailPointId) VALUES($trailPointId)";
    $result = $dbc->query($sql);
    if ($result == false || $result->rowCount() != 1) {
      $return = "insertIntersection: Query failed.";
			echo $sql.PHP_EOL;
    }
    else {
      $return = $dbc->lastInsertId();
    }
  }

  $dbc = false;
  return $return;
}

function insertIntersectionMapping($ids) {

	$return = "insertIntersectionMapping: Could not connect to database.";
	$dbc = dbConnect();
	if ($dbc != false) {
		$mapIds = array();

		for ($i = 0; $i < count($ids); $i++) {
			$id1 = $ids[$i];
			for ($j = $i + 1; $j < count($ids); $j++) {
				$id2 = $ids[$j];


				//Does it already exist?
				$sql = "SELECT * FROM IntersectionMapping WHERE intersectionId1 = $id1 AND intersectionId2 = $id2";
				$result = $dbc->query($sql);
				if ($result == false) {
					$return = "insertIntersectionMapping: Query failed.";
					$j = count($ids);
					$i = count($ids);	
				}
				else if ($result->rowCount() == 1) {
					echo "\tMapping exists.";
					$return = "insertIntersectionMapping: Mapping exists.";
				}
				else { //Doesn't exist, need to insert
				
					$sql = "INSERT INTO IntersectionMapping(intersectionId1, intersectionId2) VALUES($id1, $id2)";
					$result = $dbc->query($sql);
					if ($result == false || $result->rowCount() != 1) {
						$return = "insertIntersectionMapping: Query failed.";
						$j = count($ids);
						$i = count($ids);
					}
					else {
						$return = "insertIntersectionMapping: Insertion success.";
					}
				} //end of if else block
			}//end of j for loop
		} //end of i for loop
	}

	$dbc = false;
	return $return;
}

