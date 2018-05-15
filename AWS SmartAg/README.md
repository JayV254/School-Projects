# SmartAg
Web application for smartag monitoring system

<h3>The structure of this code is as follows:</h3>

<h4>Cognito-JS-SDK</h4>
--This folder holds the SDK for AWS Cognito. It is used for all calls to the Cognito service.

<h4>PiSensorScripts</h4>
-- This folder contains all scripts tasked with reading in data from sensors and sending them to AWS.

- readTempHumid.py is the script for reading in data from the DHT11 temperature and humidity sensor

- readLight.py is the script for reading in data from the TSL2561 light sensor

- readMoisture.py is the script for reading in data from the YL69 moisture sensor

- handle_data.py is the python script tasked with formatting the sensor data from above and sending it to AWS IoT. This file also handles automatically turning light on/off

- xxxx_data.txt files are for holding sensor data

- continuousRead.sh is a Bash script tasked with running all readxxxx.py programs above. It runs all programs in a loop, waiting 10 seconds between each loop.

- led_on.sh is a Bash script that turns the light on

- led_off.sh is a Bash script that turns the light off

<h4>PiSensorScripts</h4>
-- This folder houses the code for the front end/web site
- Included in this folder is code for the main index page, code for logging in to the site, and code for displaying the data

<h4>PiSensorScripts</h4>
-- This folder is for testing. It currently has a test call for dynamoDB.
