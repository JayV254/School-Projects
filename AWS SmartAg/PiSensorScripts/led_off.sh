#!/bin/bash

# This script uses the WiringPi library to turn the light in the system off
# This program is being used by team 19 in CS499 at the University of Kentucky. Members include Austin Vanderpool, Delbert Harrison, Jesse Vaught, Steven Liu. 
# Written by Austin Vanderpool

gpio -g write 24 0
