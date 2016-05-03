#!/usr/bin/python

import os
import sys
import csv
import mysql.connector
import argparse

#Argparse ActionClass check extension
def CheckExt(choices):
    class Act(argparse.Action):
        def __call__(self,parser,namespace,fname,option_string=None):
            ext = os.path.splitext(fname)[1][1:]
            if ext not in choices:
                option_string = '({})'.format(option_string) if option_string else ''
                if type(choices) is str:
                    parser.error("file doesn't end with {}{}".format(choices,option_string))
                elif len(choices) == 1:
                    parser.error("file doesn't end with {}{}".format(list(choices)[0],option_string))
                else:
                    parser.error("file doesn't end with one of {}{}".format(choices,option_string))
            else:
                setattr(namespace,self.dest,fname)

    return Act

#Setup argparse
parser = argparse.ArgumentParser(description='Converts csv to sql queries for The Art of Hiking', formatter_class=argparse.RawTextHelpFormatter)
parser.add_argument('csvFile', help='CSV filename', type=str, action=CheckExt('csv'))
parser.add_argument('-tn', '--tname', help='Alternate trail name, default is based on CSV filename', metavar='', type=str)
group = parser.add_mutually_exclusive_group()
group.add_argument('-f', action='store_true', default=False, help='Write SQL queries to a file')
group.add_argument('-p', action='store_true', default=False, help='Print SQL queries stdout')
args = parser.parse_args()

cnx = None
cursor = None

#Setup output
if args.p:
	pass
elif args.f:
	sys.stdout = open(args.csvFile.replace(".csv", ".sql"), "w")
	args.p = True
else:
	cnx = mysql.connector.connect(user='ghanas', password='applePie1', database='ghanas', host='dbs.eecs.utk.edu')
	cursor = cnx.cursor()

#Read in the csv
with open(args.csvFile, 'rb') as csvfile:
	spamreader = csv.reader(csvfile)
	a = [{k: float(v) for k, v in row.items()}
		for row in csv.DictReader(csvfile, skipinitialspace=True)]
	
	#Create Trail Name from args
	tName = ""
	if args.tname is None:
		tName = args.csvFile[:-4].replace("_", " ")
	else:
		tName = args.tname
	
	#Get the trailId
	tId = 0
	query = ("SELECT trailId FROM Trail WHERE trailName = %s")
	if args.p:
		print("SET @tId = (" + query % ("'" + tName + "'") + "); -- If NULL, trail not found")
		#Needs error checking for sql side
	else:
		cursor.execute(query, (tName,))
		tIdQResponse = cursor.fetchone()
		if tIdQResponse is None:
			sys.exit('Trail not found: {}'.format(tName))
		tId = tIdQResponse[0]

	tpId = 0
	for i, r in enumerate(a):

		#Always insert the TrailPoint
		query = ("INSERT INTO TrailPoint (longitudePoint, latitudePoint, elevationPoint, trailId, previousPoint) VALUES(%s, %s, %s, %s, %s)")
		if args.p:
			print(query % (r['lng'], r['lat'], r['ele'], '@tId', '@tpId' if i != 0 else 'NULL') + ";")
		else:
			cursor.execute(query, (r['lng'], r['lat'], r['ele'], tId, tpId if i != 0 else None))
		
		if i == 0:
			#Get first trailId in series
			query = ("SELECT trailPointId FROM TrailPoint ORDER BY trailId DESC LIMIT 1")
			if args.p:
				print("SET @tpId = (" + query + ");")
			else:
				cursor.execute(query)
				tpId = cursor.fetchone()[0]
		else:
			#Increment trailId
			if args.p:
				print("SET @tpId = @tpId + 1;")
			else:
				tpId += 1
		
		if i == 0 or i == (len(a) - 1):
			#Insert the TrailPoint into TrailHead
			query = ("INSERT INTO TrailHead (parkingLotId, trailPointId) VALUES(NULL, %s)")
			if args.p:
				print(query % ('@tpId') + ";")
			else:
				cursor.execute(query, (tpId,))

if args.f:
	sys.stdout.close()
else:
	cursor.close()
	cnx.close()
