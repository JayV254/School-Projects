#!/usr/bin/python
import cgi
import cgitb

def is_int_or_float(input_to_check):
    try:
    	float(input_to_check)
    except ValueError:
        return False

def validateInput(origunits,convunits,convfactor,numunits):
    # initalize parameter output color as default blue
    origunits_color = "blue"
    convunits_color = "blue"
    convfactor_color = "blue"
    numunits_color = "blue"

    error_out = ""

    # check for text fields
    if(origunits == None):
    	origunits = "None"
    	origunits_color = "red"
    	error_out = "Error: Original Unit field is blank, please select a valid choice!"
    elif(convunits == None):
    	convunits = "None"
    	convunits_color = "red"
    	error_out = "Error: Conversion Units field is blank, please select a valid choice!"
    elif(numunits == None):
        numunits = "None"
        numunits_color = "red"
        error_out = "Error: Amount of Original Units field is blank, check values and try again!"


    else:
        # check if text field input values are floats/integers
        if(is_int_or_float(numunits) == False):
            numunits_color = "red"
            error_out = "Error: Amount of Units should be a float or integer, check values and try again!" 
        if(is_int_or_float(convfactor) == False):
            convfactor_color = "red"
            error_out = "Error: Conversion factor must be float or integer, check values and try again!" 

    print ('<p style="color: ' + origunits_color + '";>Original Units: ' + str(origunits) + '</p>')
    print ('<p style="color: ' + convunits_color + '";>Converted Units: ' + str(convunits) + '</p>')
    print ('<p style="color: ' + numunits_color + '";>Amount of Units: ' + str(numunits) + '</p>')
    print ('<p style="color: ' + convfactor_color + '";>Conversion Factor: ' + str(convfactor) + '</p>')

    return error_out

def convertUnits(origunits,convunits,convfactor,numunits):
    answer = ""
    # if converting to the same units: multiply by the conversion factor
    if(origunits == convunits):
        return (float(numunits) * float(convfactor))

    # create dictionary of conversions where key is the final unit and value pair is original units followed by conversion number    
    conversions = {
          # original unit as key, conversion units as value[0] and conversion multiplier as value[1] 
          "parsec" : ["lightyear", 3.26],
          "lightyear" : ["kilometer", 3.086e13],
          "xlarn" : ["parsec", 7.3672],
          "galacticyear" : ["terrestrialyear", 250000000],
          "xarnyear" : ["terrestrialyear", 1.2579],
          "terrestrialyear" : ["terrestrialminutes", 525600] 
    }
    
    # loop through dictionary and check values for specified conversion
    for k,v in conversions.items():
        # if original units has a known conversion with requested units to be converted
        if((k == origunits) and (v[0] == convunits)):
            answer = (float(numunits) * v[1])
        elif((k == convunits) and (v[0] == origunits)):
            answer = (float(numunits) / v[1])

    return answer



    
def main():
    # print simple html opening
    print ("Content-type:text/html\r\n\r\n")
    print ('<html>')
    print ('<head>')
    print ('<title>Project 1: Unit Conversion</title>')
    print ('</head>')
    print('<body>')

    # enable error checking from cgitb library
    cgitb.enable()

    # pull form parameters and store in proper variable
    form = cgi.FieldStorage()
    origunits = form.getvalue('origunits')
    convunits = form.getvalue('convunits')
    convfactor = form.getvalue('convfactor')
    numunits = form.getvalue('numunits')
    
    # check convfactor for default
    if(convfactor == None):
        convfactor = 1.0

    # call function to check input parameters from form
    error_out = validateInput(origunits,convunits,convfactor,numunits)

    # initalize output color to default: green
    output_styling = 'style="' + 'color: green;">'

    #  if there was no error with input parameters
    if(error_out == ""):
        # attempt conversion and store answer in variable
        conversion_units = convertUnits(origunits,convunits,convfactor,numunits)
        # if conversion is not supported: set error message
        if(conversion_units == ""): 
            output_message = "Error: Sorry, that conversion is not supported!"
            output_styling = 'style="' + 'color: red; font-weight: bold;">'
        # valid input and conversion
        else:
            output_message = ("Answer: " + str(conversion_units))
    # an error was generated when testing input parameters        
    else:
        output_message = error_out
        output_styling = 'style="' + 'color: red; font-weight: bold;">'
    print ('<p ' + output_styling + output_message  + '</p>')
 
    print ('</body>')
    print ('</html>')

main()
