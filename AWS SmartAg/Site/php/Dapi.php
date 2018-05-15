<?php

require "serve.php";

if(!empty($_GET['metric']) && !empty($_GET['interval']))
{
	$measure=$_GET['metric'];
	$interval=$_GET['interval'];
	$array_return = get_data($measure, $interval);
	if(empty($array_return))
	{
  	    header('HTTP/1.1 400 No data found');
            die();
	}
	else
	{
		header('HTTP/1.1 200 Gathered');
		response($array_return);
	}
	
}
else
{
	response(NULL);
}

function response($data)
{
	$response = json_encode($data);
	echo $response;
}
?>
