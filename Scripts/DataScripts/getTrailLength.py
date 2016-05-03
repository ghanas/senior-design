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
parser.add_argument('-mm', '--mileMarker', help='Mile Marker', type=float)
args = parser.parse_args()

#Read in the csv
with open(args.csvFile, 'rb') as csvfile:
    spamreader = csv.reader(csvfile)
    a = [{k: float(v) for k, v in row.items()}
        for row in csv.DictReader(csvfile, skipinitialspace=True)]
    
    total = 0
    for i, p in enumerate(a):
        if i == 0:
            continue
        
        total = total + getDistanceFromLatLonInKm(a[i-1]['lat'], a[i-1]['lng'], a[i]['lat'], a[i]['lng']) * 0.621371
        
        if args.mileMarker is not None:
            if total > args.mileMarker:
                print 'line {}'.format(i)
                break
                
    if args.mileMarker is None:
        print '{} miles'.format(total)