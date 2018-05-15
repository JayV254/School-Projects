#! /bin/bash
# This script continuously runs the python programs that gather the sensor data. It sleeps for 30 seconds because our data does not need to be that up to date.
# This program is being used by team 19 in CS499 at the University of Kentucky. Members include Austin Vanderpool, Delbert Harrison, Jesse Vaught, Steven Liu. 
# Written by Austin Vanderpool

while [ 1 ]
do
    python readLight.py 
    python readMoisture.py 
    python readTempHumid.py 11 23 
    sleep 300 
done
