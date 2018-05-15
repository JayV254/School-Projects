
<?php
    // get DB connection
    require_once('accessDB.php');
    // Initialize the session
    session_start();
    
    $delete_sale_err = "";
    // If session variable is not set it will redirect to login page
    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
	header("location: /loginPage.php");
	exit;
    } else {
	$username = $_SESSION['username'];
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
	// if user attempted to delete a current/upcoming sale
    	if($_POST["action"] == "delete_sale") {
	    $delete_sale_sql = "DELETE FROM Sales WHERE sale_id=" . $_POST["delete_sale_id"];
            if(mysqli_query($conn, $delete_sale_sql)) {
		if($_POST["sale_type"] == "upcoming") {
		    $delete_upcoming_err = "Sale has been deleted!";
		} else {
		    $delete_history_err = "Sale has been deleted!";
		}
            } else {
		if($_POST["sale_type"] == "upcoming") {
		    $delete_upcoming_err = "Error Deleting Sale: " . mysqli_error($conn);
		} else { 
                    $delete_history_err = "Error Deleting Sale: " . mysqli_error($conn);
		}
            }
	} else if($_POST["action"] == "pay_balance") {
	    // insert payment in to Revenue table, and if succesful update the current sale balance
	    $insert_payment = "INSERT INTO Revenue (user, amount) VALUES (\"" . $username .  "\"," . (int)$_POST["balance"] . ")";
	    if(mysqli_query($conn, $insert_payment)) {
		$pay_balance_sql = "UPDATE Sales SET balance=0 WHERE Sale_id=" . $_POST["pay_sale_id"];
	        if(mysqli_query($conn, $pay_balance_sql)) {
		    if($_POST["sale_type"] == "upcoming") {
                        $delete_upcoming_err = "Sale balance paid!";
                    } else {
                        $delete_history_err = "Sale balance paid!";
                    }
	        } else {
		    if($_POST["sale_type"] == "upcoming") {
                        $delete_upcoming_err = "Error Processing Payment: " . mysqli_error($conn);
                    } else { 
                        $delete_history_err = "Error Processing Payment: " . mysqli_error($conn);
                    }
	        }
	    }
	}

    }


    // initalize variable to hold historical sales
    $history = "";
    $history_err="";
    // load up sale history
    $select = "SELECT * FROM Sales";
    $condition1_history = " WHERE username=\"" . $username . "\"";
    $condition2_history = " AND ending_sale_date<NOW()";
    $sql = $select . $condition1_history . $condition2_history;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
	    $col_name = "<td>" . "<a href=\"Sale.php?sale=" . $row["sale_id"] . "&view=mv" . "\">" .  $row["sale_name"] . "</a>" .  "</td>";
	    $col_location =  "<td>" . $row["sale_resNum"] . " " . $row["sale_street"] . " " . $row["sale_city"]. ", " . $row["sale_state"] . " " . $row["sale_zip"] . "</td>";
	    $sale_profit = "<td>$" . $row["sale_total_profit"] . "</td>";
	    $s_date_obj = new DateTime($row["starting_sale_date"]);
	    $e_date_obj = new DateTime($row["ending_sale_date"]);
	    $s_date_info = $s_date_obj->format('m-d-Y H:i');
            $e_date_info = $e_date_obj->format('m-d-Y H:i');
	    $col_date = "<td>" . $s_date_info . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $e_date_info . "</td>";
	    $sale_history_entry = $col_name . $col_location . $col_date;
	    // if user has paid their oustanding balance allow them to delete the sale
	    if($row["balance"] == "0") {
	        $delete_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
                $delete_id = "<input type=\"hidden\" name=\"delete_sale_id\" value=\"" . $row["sale_id"] . "\">";
                $delete_action = "<input type=\"hidden\" name=\"action\" value=\"delete_sale\">";
		$sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"history\">";
                $delete_submit = "<input type=\"submit\" value=\"Delete\">";
                $delete_form_close = "</form>";
		$col_delete = "<td>" . $delete_form . $delete_id . $delete_action . $sale_type . $delete_submit . $delete_form_close . "</td>";
	    } else {
		//user has an oustanding balance and must pay before they can delete the sale
		$balance = $row["balance"];
                $pay_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
                $pay_id = "<input type=\"hidden\" name=\"pay_sale_id\" value=\"" . $row["sale_id"] . "\">";
                $pay_sale_action = "<input type=\"hidden\" name=\"action\" value=\"pay_sale\">";
		$sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"history\">";
		$balance_post = "<input type=\"hidden\" name=\"balance\" value=\"" . $balance . "\">";
                $pay_submit = "<input type=\"submit\" value=\"Pay\">";
                $pay_form_close = "</form>";
                $col_delete = "<td id=\"red\">Outstanding balance: $" . $balance;
                $col_delete .=  $pay_form . $pay_id . $pay_sale_action . $sale_type . $balance_post . $pay_submit . $pay_form_close . "</td>";
	    }
	    // create sale information for table
	    $sale_history_entry = $col_name . $col_location . $col_date . $sale_profit . $col_delete;
            $history .= "<tr>" .  $sale_history_entry . "</tr>";
        }
    } else {
        $history_err = "No Previous Sales!";
    }


    // inialitize variable to hold current sales for management
    $upcoming_sales = "";
    $upcoming_sales_err = "";
    // load up current and upcoming sales
    $select = "SELECT * FROM Sales";
    $condition1_upcoming = " WHERE username=\"" . $username . "\"";
    $condition2_upcoming = " AND ending_sale_date>NOW()";
    $sql = $select . $condition1_upcoming . $condition2_upcoming;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $col_name = "<td>" . "<a href=\"Sale.php?sale=" . $row["sale_id"] . "&view=m" .  "\">" .  $row["sale_name"] . "</a>" .  "</td>";
            $col_location =  "<td>" . $row["sale_resNum"] . " " . $row["sale_street"] . " " . $row["sale_city"]. ", " . $row["sale_state"] . " " . $row["sale_zip"] . "</td>";
            $sale_profit = "<td>$" . $row["sale_total_profit"] . "</td>";
	    $s_date_obj = new DateTime($row["starting_sale_date"]);
            $e_date_obj = new DateTime($row["ending_sale_date"]);
            $s_date_info = $s_date_obj->format('m-d-Y H:i');
            $e_date_info = $e_date_obj->format('m-d-Y H:i');
            $col_date = "<td>" . $s_date_info . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $e_date_info . "</td>";
            $delete_sale_sql = "DELETE FROM Sales WHERE sale_id=\"" . row["sale_id"] . "\"";
	    // if user has paid their oustanding balance allow them to delete the sale
            if($row["balance"] == "0") {
                $delete_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
                $delete_id = "<input type=\"hidden\" name=\"delete_sale_id\" value=\"" . $row["sale_id"] . "\">";
                $delete_action = "<input type=\"hidden\" name=\"action\" value=\"delete_sale\">";
		$sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"upcoming\">";
                $delete_submit = "<input type=\"submit\" value=\"Delete\">";
                $delete_form_close = "</form>";
                $col_delete = "<td>" . $delete_form . $delete_id . $delete_action . $sale_type . $delete_submit . $post_sale . $delete_form_close . "</td>";
            } else {
	        //user has an oustanding balance and must pay before they can delete the sale
		$balance = $row["balance"];
                $pay_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
                $pay_id = "<input type=\"hidden\" name=\"pay_sale_id\" value=\"" . $row["sale_id"] . "\">";
                $pay_sale_action = "<input type=\"hidden\" name=\"action\" value=\"pay_balance\">";
		$sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"upcoming\">";
		$balance_post = "<input type=\"hidden\" name=\"balance\" value=\"" . $balance . "\">";
                $pay_submit = "<input type=\"submit\" value=\"Pay\">";
                $pay_form_close = "</form>";
                $col_delete = "<td id=\"red\">Outstanding balance: $" . $balance;
                $col_delete .=  $pay_form . $pay_id . $pay_sale_action . $sale_type . $balance_post . $pay_submit . $pay_form_close . "</td>";
            }

            $upcoming_sales_entry = $col_name . $col_location . $col_date . $sale_profit .  $col_delete;
            $upcoming_sales .= "<tr>" .  $upcoming_sales_entry . "</tr>";
        }
    } else {
        $upcoming_sales_err = "No Upcoming Sales!";
    }
    
    // get up to date ad costs from ad table for use in creating sale and dynamic promo_tier select statement
    $get_ad_cost = "SELECT * FROM AdvertCost";
    $ad_result = $conn->query($get_ad_cost);
    if($ad_result->num_rows > 0) {
	while($ad_row = $ad_result->fetch_assoc()) {
	    if($ad_row["tier"] == "1") {
		$tier1_cost = $ad_row["tier_cost"];
	    } else if($ad_row["tier"] == "2") {
		$tier2_cost = $ad_row["tier_cost"];
	    } else if($ad_row["tier"] == "3") {
		$tier3_cost = $ad_row["tier_cost"];
	    }
	}
    } else {
	$ad_result_err = mysqli_error($conn);
    }

    $conn->close();

?>

<!DOCTYPE html>
<html>
<title>YSC User Dashboard</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue-grey.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>

strong {
	font-weight: bold; 
}

em {
	font-style: italic; 
}

table {
	background: #f5f5f5;
	border-collapse: separate;
	box-shadow: inset 0 1px 0 #fff;
	font-size: 12px;
	line-height: 24px;
	margin: 30px auto;
	text-align: left;
	width: 900px;
}	

th {
	background: url(https://jackrugile.com/images/misc/noise-diagonal.png), linear-gradient(#777, #444);
	border-left: 1px solid #555;
	border-right: 1px solid #777;
	border-top: 1px solid #555;
	border-bottom: 1px solid #333;
	box-shadow: inset 0 1px 0 #999;
	color: #fff;
  font-weight: bold;
	padding: 10px 15px;
	position: relative;
	text-shadow: 0 1px 0 #000;	
}

th:after {
	background: linear-gradient(rgba(255,255,255,0), rgba(255,255,255,.08));
	content: '';
	display: block;
	height: 25%;
	left: 0;
	margin: 1px 0 0 0;
	position: absolute;
	top: 25%;
	width: 100%;
}

th:first-child {
	border-left: 1px solid #777;	
	box-shadow: inset 1px 1px 0 #999;
}

th:last-child {
	box-shadow: inset -1px 1px 0 #999;
}

#red {
	color:red;
}

td {
	border-right: 1px solid #fff;
	border-left: 1px solid #e8e8e8;
	border-top: 1px solid #fff;
	border-bottom: 1px solid #e8e8e8;
	padding: 10px 15px;
	position: relative;
	transition: all 300ms;
}

td:first-child {
	box-shadow: inset 1px 0 0 #fff;
}	

td:last-child {
	border-right: 1px solid #e8e8e8;
	box-shadow: inset -1px 0 0 #fff;
}	

tr {
	background: url(https://jackrugile.com/images/misc/noise-diagonal.png);	
}

tr:nth-child(odd) td {
	background: #f1f1f1 url(https://jackrugile.com/images/misc/noise-diagonal.png);	
}

tr:last-of-type td {
	box-shadow: inset 0 -1px 0 #fff; 
}

tr:last-of-type td:first-child {
	box-shadow: inset 1px -1px 0 #fff;
}	

tr:last-of-type td:last-child {
	box-shadow: inset -1px -1px 0 #fff;
}	

tbody:hover td {
	color: transparent;
	text-shadow: 0 0 3px #aaa;
}

tbody:hover tr:hover td {
	color: #444;
	text-shadow: 0 1px 0 #fff;
}

#input { margin-bottom:4px}
#formtag { margin-bottom:0px}
body {background-image: url("/images/HomeBG.jpg")}
html,body,h1,h2,h3,h4,h5 {font-family: "Open Sans", sans-serif}
</style>
<body class="w3-theme-l5">

<!-- Navbar -->
<div class="w3-top">
 <div class="w3-bar w3-theme-d2 w3-left-align w3-large">
  <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-hover-white w3-large w3-theme-d2" href="homepage.php"><i class="fa fa-bars"></i></a>
  <a href="homepage.php" class="w3-bar-item w3-button w3-padding-large w3-theme-d4"><i class="fa fa-home w3-margin-right"></i>( YSC )</a>
  <div class="w3-dropdown-hover w3-hide-small">
    <button class="w3-button w3-padding-large" title="UserOptions"><i class="fa fa-user-circle"></i></button>     
    <div class="w3-dropdown-content w3-card-4 w3-bar-block" style="width:300px">
      <a href="#" class="w3-bar-item w3-button">Edit Profile<i class="w3-right fa fa-cog"></i></a>
      <a href="logout.php" class="w3-bar-item w3-button">Sign Out<i class="w3-right fa fa-sign-out"></i></a>
    </div>
  </div>
 </div>
</div>

<!-- Page Container -->
<div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">    
  <!-- The Grid -->
  <div class="w3-row">
    <!-- Left Column -->
    <div class="w3-col m3">
      <!-- Profile -->
      <div class="w3-card w3-round w3-white">
        <div class="w3-container">
         <h4 class="w3-center">Hello <?php echo $_SESSION["username"] ?>!</h4>
         <p class="w3-center"><img src="images/avatar.png" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
         <hr>
         <p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i>YSC User</p>
        </div>
      </div>
      <br>

      <!-- Accordion -->
      <div class="w3-card w3-round">
        <div class="w3-white">
	  <h4 class="w3-center" style="padding-top:8px">Sale Hub</h4>
          <button style="border:8px solid black" data-toggle="modal" data-target="#CreateSale" class="w3-button w3-block w3-theme-l1 w3-left-align"><i style="color:black" class="fa fa-circle-o-notch fa-fw w3-margin-right"></i> Create a Sale</button>
            <div class="modal fade" id="CreateSale" role="dialog">
              <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content" style="overflow:scroll;height:500px">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Create a YS event</h4>
                </div>
                <div class="modal-body">
                  <form method="post" action="CreateSale.php">
		    <p id="formtag">Sale Title/Name & Promotional Tier:</p>
  		    <input id="input" type="text" name="sale_name" minlength="4" maxlength="50" required><br>
		    <select name="promo_tier">
		      <option value="1|<?=$tier1_cost?>">Tier 1-- $<?php echo $tier1_cost;?></option>
		      <option value="2|<?=$tier2_cost?>">Tier 2-- $<?php echo $tier2_cost;?></option>
		      <option selected="selected" value="3|<?=$tier3_cost?>">Tier 3-- $<?php echo $tier3_cost;?></option>
		    </select>
		    <p id="formtag">Contact Number(10-digit):</p>
		    <input id="input" type="text" name="sale_contact" minlength="10" maxlength="10" required><br>
		    <p id="formtag">House/Apt Number:</p>
		    <input id="input" type="text" name="sale_resNum" required><br>
		    <p id="formtag">Street:</p>
		    <input id="input" type="text" name="sale_street" required><br>
	            <p id="formtag">City/State:</p>
		    <input id="input" type="text" name="sale_city" required>
		    <input id="input" type="text" name="sale_state" required><br>
	 	    <p id="formtag">Zip Code:</p>
                    <input id="input" type="text" size="5" name="sale_zip" minlength="5" maxlength="5" required><br>
		    <p id="formtag">Starting-Day/Month/Year(DD/MONTH/YYYY): </p>
		    <input id="input" type="text" size="2" name="s_sale_day" min="1" max="31" minlength="2" maxlength="2" required>
		    <select name="s_sale_month" required>
		      <option value="01">January</option>
		      <option value="02">Febuary</option>
		      <option value="03">March</option>
		      <option value="04">April</option>
		      <option value="05">May</option>
                      <option value="06">June</option>
                      <option value="07">July</option>
                      <option value="08">August</option>
		      <option value="09">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
		    </select>
		    <input id="input" type="text" size="4" name="s_sale_year" minlength="4" maxlength="4" min="<?php echo date("Y"); ?>" required><br>
		    <p id="formtag">Starting-Hour/Minute(HH:MM): </p>
		    <input id="input" size="2" type="text" size="2" name="s_sale_hour" min="1" max="12" minlength="2" required><span> : </span>
		    <input id="input" size="2" type="text" size="2" name="s_sale_minute" min="0" max="59" minlength="2" required>
 		    <p id="formtag">Ending-Day/Month/Year(DD/MONTH/YYYY): </p>
                    <input id="input" type="text" size="2" name="e_sale_day" min="1" max="31" minlength="2" maxlength="2" required>
                    <select name="e_sale_month" required>
                      <option value="01">January</option>
                      <option value="02">Febuary</option>
                      <option value="03">March</option>
                      <option value="04">April</option>
                      <option value="05">May</option>
                      <option value="06">June</option>
                      <option value="07">July</option>
                      <option value="08">August</option>
                      <option value="09">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
                    </select>
                    <input id="input" type="text" size="4" name="e_sale_year" minlength="4" maxlength="4" min="<?php echo date("Y"); ?>" required><br>
                    <p id="formtag">Ending-Hour/Minute(HH:MM): </p>
	            <input id="input" size="2" type="text" size="2" name="e_sale_hour" min="1" max="12" minlength="2" required><span> : </span>
                    <input id="input" size="2" type="text" size="2" name="e_sale_minute" min="0" max="59" minlength="2" required>
		    <br><br><input type="submit" value="Submit & Pay">
		    <input type="reset" value="Reset">
		  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

	      </div>
	      </div>
            </div>
	</div>
      </div>
      <br>

    <!-- End Left Column -->
    </div>

    <!-- Middle Column -->
    <div class="w3-col m9">
      <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
        <h2 style="text-decoration:underline;text-align:center">Sale History</h2>
	<p style="text-align:center">Click Sale Title to View Sale</p>
	<p style="text-align:center"><?php echo $delete_history_err; ?></p>
        <p style="text-align:center"><?php echo $history_err; ?></p>
        <table class="historyTable">
          <thead>
    	    <tr>
              <th>Sale Title</th>
              <th>Location</th>
              <th>Start Date - End Date</th>
	      <th>Sale Profit</th>
	      <th>Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php echo $history; ?>
          </tbody>
        </table>
      </div>
      <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
        <h2 style="text-decoration:underline;text-align:center">Upcoming/Current Sales</h2>
	<p style="text-align:center">Click Sale Title to Manage Sale</p>
        <p style="text-align:center"><?php echo $delete_upcoming_err; ?></p>
        <p style="text-align:center"><?php echo $upcoming_sales_err; ?></p>
        <table class="historyTable">
          <thead>
            <tr>
              <th>Sale Title</th>
              <th>Location</th>
              <th>Start Date - End Date</th>
	      <th>Sale Profit</th>
	      <th>Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php echo $upcoming_sales ?>
          </tbody>
        </table>
      </div>

    <!-- End Middle Column -->
    </div>
  <!-- End Grid -->
  </div>

<!-- End Page Container -->
</div>
<br>

<!-- Footer Required for License -->
<footer class="w3-container w3-theme-d5" style="margin-top:100%">
  <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a></p>
</footer>

</body>
</html> 

