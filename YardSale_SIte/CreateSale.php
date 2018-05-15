<?php
// Include config file
require_once 'accessDB.php';

session_start();

$username = $_SESSION['username'];
// Define variables and initialize with empty values
    $error_flag = false;
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve variables from POST request submitted via the web form
    $s_sale_date_day = $_POST["s_sale_day"];
    $s_sale_date_month = $_POST["s_sale_month"];
    $s_sale_date_year = $_POST["s_sale_year"];
    $s_sale_date_hour = $_POST["s_sale_hour"];
    $s_sale_date_minute = $_POST["s_sale_minute"];
    $e_sale_date_day = $_POST["e_sale_day"];
    $e_sale_date_month = $_POST["e_sale_month"];
    $e_sale_date_year = $_POST["e_sale_year"];
    $e_sale_date_hour = $_POST["e_sale_hour"];
    $e_sale_date_minute = $_POST["e_sale_minute"];
    $sale_street = $_POST["sale_street"];
    $sale_resNum = $_POST["sale_resNum"];
    $sale_zip = $_POST["sale_zip"];
    $sale_name = $_POST["sale_name"];
    $promo_info = $_POST["promo_tier"];
    $promo_explode = explode('|', $promo_info);
    $promo_tier = $promo_explode[0];
    $ad_cost = $promo_explode[1];
    $sale_contact = $_POST["sale_contact"];
    $sale_city = $_POST["sale_city"];
    $sale_state = $_POST["sale_state"];
    $s_sale_date = $s_sale_date_year . "-" . $s_sale_date_month . "-" . $s_sale_date_day . " " .  $s_sale_date_hour . ":" . $s_sale_date_minute . ":" . "00";
    $e_sale_date = $e_sale_date_year . "-" . $e_sale_date_month . "-" . $e_sale_date_day . " " .  $e_sale_date_hour . ":" . $e_sale_date_minute . ":" . "00";
    // Check input errors before inserting in database
    if($error_flag == false) {
        // Prepare an insert statement
        $sql = "INSERT INTO Sales (promo_tier, sale_name, sale_contact, sale_street, starting_sale_date, sale_city, sale_state, sale_resNum, sale_zip, ending_sale_date, username, ad_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	if($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssssssss", $promo_tierP, $sale_nameP, $sale_contactP, $sale_streetP, $s_sale_dateP, $sale_cityP, $sale_stateP, $sale_resNumP, $sale_zipP, $e_sale_dateP, $usernameP, $ad_costP);
	    // set bind parameters
	    $sale_stateP = $sale_state;
	    $sale_cityP = $sale_city;
	    $sale_nameP = $sale_name;
	    $promo_tierP = $promo_tier;
	    $sale_contactP = $sale_contact;
	    $s_sale_dateP = $s_sale_date;
	    $sale_streetP = $sale_street;
	    $sale_resNumP = $sale_resNum;
	    $sale_zipP = $sale_zip;
	    $e_sale_dateP = $e_sale_date;
            $usernameP = $username;
	    $ad_costP = $ad_cost;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Add payment to revenue table
		$insert_payment = "INSERT INTO Revenue (user, amount) VALUES (\"" . $username .  "\"," . (int)$ad_cost . ")";
		if(mysqli_query($conn,$insert_payment)) {
			header("location: dashboard.php");
			mysqli_stmt_close($stmt);
        		mysqli_stmt_close($conn);
		}
            } else{
		echo "yard sale not created";
                echo mysqli_error($conn);
            }
        }
        // Close statement and connection
	//redirect back to homepage
    }
}
?>

