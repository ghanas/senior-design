#!/usr/bin/python

import os
import sys
import csv
import argparse
import math

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

#Function http://stackoverflow.com/questions/27928/calculate-distance-between-two-latitude-longitude-points-haversine-formula
def getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2):
  R = 6371
  dLat = deg2rad(lat2-lat1)
  dLon = deg2rad(lon2-lon1)
  a = math.sin(dLat/2) * math.sin(dLat/2) + math.cos(deg2rad(lat1)) * math.cos(deg2rad(lat2)) * math.sin(dLon/2) * math.sin(dLon/2)
  c = 2 * math.atan2(math.sqrt(a), math.sqrt(1-a))
  d = R * c
  return d

def deg2rad(deg):
  return deg * (math.pi/180)

#Setup argparse
parser = argparse.ArgumentParser(description='Converts csv to sql queries for The Art of Hiking', formatter_class=argparse.RawTextHelpFormatter)
parser.add_argument('csvFile', help='CSV filename', type=str, action=CheckExt('csv'))
parser.add_argument('mileMarker', help='Mile Marker', type=float)
args = parser.parse_args()

#Read in the csv
with open(args.csvFile, 'rb') as csvfile:
    csvreader = csv.reader(csvfile, quotechar='\'')

    csvFile = []

    try:
        for row in csvreader:
            csvFile.append(row)
    except csv.Error as e:
        sys.exit('file %s, line %d: %s' % (filename, reader.line_num, e))
    
    total = 0
    for i, row in enumerate(csvFile):
        if i == 0 or i == 1:
            continue
        
        total = total + getDistanceFromLatLonInKm(float(csvFile[i-1][0]), float(csvFile[i-1][1]), float(csvFile[i][0]), float(csvFile[i][1])) * 0.621371

        if total > args.mileMarker:
            print "{}, {}, {}".format(row[0], row[1], row[2])
            break
