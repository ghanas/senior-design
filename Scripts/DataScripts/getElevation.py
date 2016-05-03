#!/usr/bin/python

import sys
import os.path
import argparse
import googlemaps

#Setup argparse
parser = argparse.ArgumentParser(description='Get elevation for a point', formatter_class=argparse.RawTextHelpFormatter)
parser.add_argument('lat', help='Latitude Point', type=float)
parser.add_argument('lon', help='Longitude Point', type=float)
args = parser.parse_args()

#Calculate the elevations
failed = False
client = googlemaps.Client("AIzaSyCwpNH6v6fkNXIa40AUqI80cV4DXRizGAg")
try:
    elevation = client.elevation((args.lat, args.lon))
    print elevation[0]['elevation']
except:
    print >> sys.stderr, 'Failed to get elevation for ({}, {})'.format(args.lat, args.lon)
