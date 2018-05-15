# This python program handles reading in the data, formatting it, and sending it to AWS IoT. It also handles automatically controlling the light in the system.
# This program is being used by team 19 in CS499 at the University of Kentucky. Members include Austin Vanderpool, Delbert Harrison, Jesse Vaught, Steven Liu. 
# Written by Austin Vanderpool
 

# Import SDK packages
from AWSIoTPythonSDK.MQTTLib import AWSIoTMQTTClient
#Import datetime
from datetime import datetime
import time, sys
import subprocess


# For certificate based connection
myMQTTClient = AWSIoTMQTTClient("austin_pi")
# Configurations
# For TLS mutual authentication
myMQTTClient.configureEndpoint("a1fkyjx4vhssyi.iot.us-east-2.amazonaws.com", 8883)
myMQTTClient.configureCredentials("/home/pi/.local/lib/python2.7/site-packages/AWSIoTPythonSDK/certs/rootCA.crt", "/home/pi/.local/lib/python2.7/site-packages/AWSIoTPythonSDK/certs/0520a951e6-private.pem.key", "/home/pi/.local/lib/python2.7/site-packages/AWSIoTPythonSDK/certs/0520a951e6-certificate.pem.crt.txt")
# myMQTTClient.configureCredentials("YOUR/ROOT/CA/PATH")
myMQTTClient.configureOfflinePublishQueueing(-1)  # Infinite offline Publish queueing
myMQTTClient.configureDrainingFrequency(2)  # Draining: 2 Hz
myMQTTClient.configureConnectDisconnectTimeout(10)  # 10 sec
myMQTTClient.configureMQTTOperationTimeout(5)  # 5 sec

#Below are the files that hold the sensor data (local directory)
tempFile = "temperature_data.txt"
moistureFile = "moisture_data.txt"
lightFile = "light_data.txt"
humidFile = "humidity_data.txt"
sensor_files = [tempFile, moistureFile, lightFile, humidFile] # List of file names

#This function is used to verify data integrity. Sometimes the data gets read at the same time as other programs are writing, leading to empty sensor values.
def checkDataSet(dataList):
	for value in dataList:
		if (value == ''):
			return False
	return True

#This function reads all data from the sensor_files list. Returns list of sensor data (temp, moisture, light, humidity)
def readSensorData():
	data = []
	for File in sensor_files:
		dataFile = open(File, 'r')
		data.append(dataFile.readline().rstrip())
	return data

#This function automatically turns light in system on/off based on temperature
def automateLight(temp):
	if(temp < 23):
		subprocess.call("./led_on.sh",shell=True)
	if( temp > 26):
		subprocess.call("./led_off.sh",shell=True)
	
#This function handles IoT stuff. It connects to AWS, builds the data message in JSON format and sends it up
def sendToAWS(dataList):
	try:
		myMQTTClient.connect()
		if(printData == "Y"):
			print(dataList)
		if(checkDataSet(dataList)):
			msg = '"DateTime": "{}","Date_Time": "{}", "Temperature": "{}", "Moisture": "{}", "Light": "{}", "Humidity": "{}"'.format(str(datetime.now()),str(datetime.now()),float(dataList[0]),int(dataList[1]),int(dataList[2]),float(dataList[3]))
			msg = '{'+msg+'}'
			myMQTTClient.publish("$aws/things/austin_pi/shadow/update", msg, 1)
		myMQTTClient.disconnect()
	except:
		print("Connection failed, retrying...")

print ""
print "------------ Send Data to AWS ------------"
print "\n"

printData = raw_input("Would you like to print sensor data to the command line? (Y/N : q to quit) ")
print ""

while(printData != "Y" and printData != "N" and printData != "q"):
	printData = raw_input("Would you like to print sensor data to the command line? (Y/N : q to quit) ")
	print ""
	print printData

if(printData != "q"):
	print "ctrl+c to exit this program at any time"
	print "\n"
	delay = input("Enter number of seconds between each publish to AWS: ")
	print ""

#Main loop. Continuously read in sensor data, automate light, send data to AWS. Sleeps based on user input
while(printData == "Y" or printData == "N"):
	try:
		dataList = readSensorData()
		if(checkDataSet(dataList)):
			automateLight(float(dataList[0]))
		sendToAWS(dataList)
		time.sleep(delay)
	except KeyboardInterrupt:
		print "\n Exiting..."
        	sys.exit()
print "Exiting..."
