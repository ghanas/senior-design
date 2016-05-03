#!/usr/bin/python

import sys
import os.path
import argparse
import googlemaps
import xml.etree.ElementTree as ET

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

#Argparse ActionClass clamps ppr to (0, 80]
class clamp(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        if values <= 0 or values > 80:
            print >> sys.stderr, 'PointsPerRequest must be within 1 and 80. Defaulted to 80.'
            setattr(namespace, self.dest, 80)
        else:
            setattr(namespace, self.dest, values)

#Setup argparse
parser = argparse.ArgumentParser(description='Convert a GPX file to CSV\nWill also calculate elevations', formatter_class=argparse.RawTextHelpFormatter)
parser.add_argument('gpxFile', help='GPX filename', type=str, action=CheckExt('gpx'))
parser.add_argument('-p', '--ppr', help='Number of points to request at a time', type=int, metavar='PointsPerRequest', default=80, action=clamp)
args = parser.parse_args()

#Parse the gpx files
tree = ET.parse(args.gpxFile)
root = tree.getroot()
points = []

#Typically has elevations
for trkpt in root.iter('{http://www.topografix.com/GPX/1/1}trkpt'):
    if len(trkpt) > 0:
        points.append({'LatLng': (float(trkpt.attrib['lat']), float(trkpt.attrib['lon'])), 'ele': float(trkpt[0].text)})
    else:
        points.append({'LatLng': (float(trkpt.attrib['lat']), float(trkpt.attrib['lon']))})

#Typically doesn't have elevations
for rtept in root.iter('{http://www.topografix.com/GPX/1/1}rtept'):
    if len(rtept) > 0:
        points.append({'LatLng': (float(rtept.attrib['lat']), float(rtept.attrib['lon'])), 'ele': float(rtept[0].text)})
    else:
        points.append({'LatLng': (float(rtept.attrib['lat']), float(rtept.attrib['lon']))})

#Loop through the points looking for points without elevations
pointsWithoutElevation = []
for p in points:
    if 'ele' not in p:
        pointsWithoutElevation.append(p)

#Calculate the elevations
failed = False
client = googlemaps.Client("AIzaSyCwpNH6v6fkNXIa40AUqI80cV4DXRizGAg")
if len(pointsWithoutElevation) != 0:
    for i in range(len(pointsWithoutElevation) / args.ppr + 1):
        try:
            elevations = client.elevation(map(lambda p: p['LatLng'], pointsWithoutElevation[i*args.ppr:(i+1)*args.ppr]))
            for j, e in enumerate(elevations):
                pointsWithoutElevation[i*args.ppr+j]['ele'] = e['elevation']
        except:
            print >> sys.stderr, 'Failed with the request for range of locations from {} to {}'.format(i*args.ppr, (i+1)*args.ppr)
            print >> sys.stderr, 'CSV file will not have elevations'
            failed = True

#Write the data to a csv
f = open(args.gpxFile.replace(".gpx", ".csv"), "w")
f.write('lat,lng,ele')
for p in points:
    lat = str(p['LatLng'][0]).rstrip('0').rstrip('.')
    lng = str(p['LatLng'][1]).rstrip('0').rstrip('.')
    if not failed:
        ele = str(p['ele']).rstrip('0').rstrip('.')
        f.write('\n%s,%s,%s' % (lat, lng, ele))
    else:
        f.write('\n%s,%s,' % (lat, lng))
f.close()
