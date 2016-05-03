import json
import googlemaps
import sys
import os

with open('out.json', "r") as data_file:
    data = json.load(data_file)
    trailCount = {}

    for k, trail in enumerate(data['features']):
        points = []

        for coord in trail['geometry']['coordinates']:
            points.append({'LatLng': (float(coord[1]), float(coord[0]))})

        #Calculate the elevations
        failed = False
        #client = googlemaps.Client("AIzaSyCwpNH6v6fkNXIa40AUqI80cV4DXRizGAg")
        client = googlemaps.Client("AIzaSyDsoAdh_j5VAXTls9Xx6hHoug-0sq4YkHQ")
        for i in range(len(points) / 80 + 1):
            try:
                elevations = client.elevation(map(lambda p: p['LatLng'], points[i*80:(i+1)*80]))
                for j, e in enumerate(elevations):
                    points[i*80+j]['ele'] = e['elevation']
            except:
                print >> sys.stderr, 'Failed with the request for range of locations from {} to {} from {}'.format(i*80, (i+1)*80, trailName)
                print >> sys.stderr, 'CSV file will not have elevations'
                failed = True

        trailName = trail['properties']['TRAILNAME']

        if trailName in trailCount:
            trailCount[trailName] += 1
        else:
            trailCount[trailName] = 0

        with open(trailName + " " + str(trailCount[trailName]) + ".csv", "w") as f:
            f.write('lat,lng,ele')
            for p in points:
                lat = str(p['LatLng'][0]).rstrip('0').rstrip('.')
                lng = str(p['LatLng'][1]).rstrip('0').rstrip('.')
                if not failed:
                    ele = str(p['ele']).rstrip('0').rstrip('.')
                    f.write('\n%s,%s,%s' % (lat, lng, ele))
                else:
                    f.write('\n%s,%s,' % (lat, lng))

            print("{:6.2f} {}".format(float(k) / len(data['features']) * 100, trailName + " " + str(trailCount[trailName]) + ".csv"))
            sys.stdout.flush()
