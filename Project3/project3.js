// CS316 Project3
// Author: Jesse Vaught
// This example uses a named function serveURL() for the callback function
// sent to the createServer() method.

var http = require("http"),
	url = require('url');
var fs = require('fs');
const port = 3456;
const hostname = 'feverfew.cs.uky.edu';
//const port_min = 2000;
//const port_max = 30000;
//const port = Math.floor(Math.random()*(port_max-port_min+1)+port_min);
console.log("Server started. Listing on http:\/\/" + hostname + ":" + port);

// Serves out the file, or 33% of the time serve an advertisement
function serveFile(fileName, response) {

	var random = Math.random();
	// if random number is less than or equal to 0.33 return the advert, else give file
	if(random <= 0.33) {
		// give advert
		response.setHeader('Content-Type', 'image/jpeg');
		fileName = "advert.jpg";
	}

	fs.readFile(fileName, (err, data) => {
        	if (err) throw err;
                response.end(data);
        });
}

function validate(xurl) {
	// check against regular expressions
	var regexMP3 = /^\/[a-zA-Z0-9_]+\.mp3$/
	var regexJPG = /^\/[a-zA-Z0-9_]+\.jpg$/

	var fileType
	if(xurl[0] != '/') {
		fileType = false;
		return fileType
	} else {
		if(regexMP3.test(xurl)) {
			// The requested url is accepted and wants mp3 return
			fileType = "mp3";
		} else if(regexJPG.test(xurl)) {
			// The requested url is accepted and wants jpg return
			fileType = "jpg";
		} else {
			fileType = false;
		}
		return fileType;
	}
}

function serveURL(request, response) {
	var xurl = request.url;
	console.log(xurl);
	var fileType = validate(xurl);
	if(fileType == false) {
		// bad file name;
		console.log("bad file name");
		response.setHeader('Content-type', 'text/plain');
		response.statusCode = 400;
		response.end('Error 400! The URL you requested is incorrect! Requested URL: '+xurl+'');
	} else {
		// good file name
		console.log("good file request");
		response.statusCode = 200;
		switch (fileType) {
			case "mp3":
				response.setHeader('Content-Type', 'audio/mpeg');
				console.log("mp3 requested");
				break;
			case "jpg":
				response.setHeader('Content-Type', 'image/jpeg');
				console.log("jpg requested");
				break;
		}
		var fileName
        	// if the first character in url is slash, remove it
        	if(xurl.charAt(0) == '/') {
                	fileName = xurl.substr(1);
			console.log(fileName);
			// if filename exists serve it up
			if(fs.existsSync(fileName)) {
				console.log("file exists");
				serveFile(fileName, response);
			} else {
				console.log("file does not exist");
				// file doesn't exist
				response.statusCode = 404;
				response.setHeader('Content-type', 'text/plain');
				response.end('Error 404, file does not exist!');
			}
        	}
	}
	server.close();
}

var server = http.createServer(serveURL);
server.listen(port,hostname);

