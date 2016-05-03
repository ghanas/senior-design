#! /usr/bin/env bash

echo 'Inserting regions';
php insertRegion.php

echo -e '\n\nInserting trails';
php insertTrail.php

php insertInterestPointType.php

echo -e '\n\nInserting trail points';
./insertTrailPoints.bash
php insertInterestPoint.php

echo -e '\n\nInserting parking lots';
php insertParkingLot.php
php insertIntersection.php
