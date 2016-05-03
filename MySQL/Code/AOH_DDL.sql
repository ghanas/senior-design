CREATE TABLE Region
(
    regionId int NOT NULL AUTO_INCREMENT,
    regionName Varchar(255),

    PRIMARY KEY (regionId)
);
CREATE TABLE Trail
(
	trailId int NOT NULL AUTO_INCREMENT,
	trailName varchar(255) NOT NULL,
	trailLength DOUBLE(4,1) NOT NULL,
	trailDifficulty DOUBLE(3,2),
	elevationGain DOUBLE(6,2) NOT NULL,
	horseAccessible int NOT NULL,
	trailDescription varchar(3000),
	isLoop tinyint NOT NULL,

	PRIMARY KEY (trailId)
);
CREATE TABLE Point
(
    pointId int NOT NULL AUTO_INCREMENT,
    longitudePoint DOUBLE(8,5),
    latitudePoint DOUBLE(8,5),
    elevationPoint DOUBLE(6,2),

    PRIMARY KEY (pointId)
);
CREATE TABLE Intersection
(
	intersectionId int NOT NULL AUTO_INCREMENT,
    trailPointId int NOT NULL,

	PRIMARY KEY (intersectionId),
    FOREIGN KEY (trailPointId) REFERENCES TrailPoint(trailPointId)
);
CREATE TABLE InterestPointType
(
    typeId int NOT NULL AUTO_INCREMENT,
    description varchar(50) NOT NULL,

    PRIMARY KEY (typeId)
);
CREATE TABLE InterestPoint
(
    interestPointId int NOT NULL AUTO_INCREMENT,
    interestPointName varchar(255),
    typeId int NOT NULL,
    pointId int,

    PRIMARY KEY (interestPointId),
    FOREIGN KEY (typeId) REFERENCES InterestPointType(typeId),
    FOREIGN KEY (pointId) REFERENCES Point(pointId)
);
CREATE TABLE ParkingLot
(
    parkingLotId int NOT NULL AUTO_INCREMENT,
    maxLotCompacity int,
    pointId int NOT NULL,

    PRIMARY KEY (parkingLotId),
    FOREIGN KEY (pointId) REFERENCES Point(pointId)
);
CREATE TABLE TrailPoint
(
    trailPointId int NOT NULL AUTO_INCREMENT,
    pointId int NOT NULL,
    trailId int NOT NULL,
    previousPoint int,

    PRIMARY KEY (trailPointId),
    FOREIGN KEY (pointId) REFERENCES Point(pointId),
    FOREIGN KEY (trailId) REFERENCES Trail(trailId),
    FOREIGN KEY (previousPoint) REFERENCES TrailPoint(trailPointId)
);
CREATE TABLE TrailHead
(
    trailHeadId int NOT NULL AUTO_INCREMENT,
    trailPointId int NOT NULL,

    PRIMARY KEY (trailHeadId),
    FOREIGN KEY (trailPointId) REFERENCES TrailPoint(trailPointId)
);
CREATE TABLE Warnings
(
    warningId int NOT NULL,
    trailId int,
    regionId int NOT NULL,
    dateEntered DateTime NOT NULL,

    PRIMARY KEY (warningId),
    FOREIGN KEY (trailId) REFERENCES Trail(trailId),
    FOREIGN KEY (regionId) REFERENCES Region(regionId)
);
CREATE TABLE Users
(
	userId int NOT NULL AUTO_INCREMENT,
	username varchar(255) NOT NULL,
	password varchar(255) NOT NULL,
	email varchar(255) NOT NULL,

	PRIMARY KEY (userId),
	UNIQUE(username),
	UNIQUE(email)
);
CREATE TABLE Review
(
	reviewId int NOT NULL AUTO_INCREMENT,
	userId int NOT NULL,
	trailId int NOT NULL,
	userRating int,
	userReview text,

	PRIMARY KEY (reviewId),

	FOREIGN KEY (userId) REFERENCES Users(userId),
	FOREIGN KEY (trailId) REFERENCES Trail(trailId)
);
CREATE TABLE IntersectionMapping
(
    intersectionId1 int NOT NULL,
    intersectionId2 int NOT NULL,

    FOREIGN KEY (intersectionId1) REFERENCES Intersection(intersectionId),
    FOREIGN KEY (intersectionId2) REFERENCES Intersection(intersectionId)
);
CREATE TABLE InterestPointTrailMapping
(
    trailId int NOT NULL,
    interestPointId int NOT NULL,

    FOREIGN KEY (interestPointId) REFERENCES InterestPoint(interestPointId),
    FOREIGN KEY (trailId) REFERENCES Trail(trailId)
);
CREATE TABLE ParkingLotTrailMapping
(
    parkingLotId int NOT NULL,
    trailId int NOT NULL,

    FOREIGN KEY (parkingLotId) REFERENCES ParkingLot(parkingLotId),
    FOREIGN KEY (trailId) REFERENCES Trail(trailId)
);
CREATE TABLE RegionTrailMapping
(
    regionId int NOT NULL,
    trailId int NOT NULL,

    FOREIGN KEY (regionId) REFERENCES Region(regionId),
    FOREIGN KEY (trailId) REFERENCES Trail(trailId)
);
