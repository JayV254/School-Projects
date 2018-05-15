<?php

    /* Define the credentials to login to our DB for all other functionality*/
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'Kissy1989');
    define('DB_NAME', 'YardSaleProject');

    // Attempt to connect to MySQL database with above definitions *make sure to use updated mysqli_connect*
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
    if($conn === false) {
	echo "couldn't connected to db";
	die("ERROR: Could not connect. " . mysqli_connect_error());
    }
?>

