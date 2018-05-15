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
	} else if($_POST["action"] == "update_tier_cost") {
	    $tier1_price = $_POST["tier1"];
            $tier2_price = $_POST["tier2"];
            $tier3_price = $_POST["tier3"];

	    $update_tier1 = "UPDATE AdvertCost SET tier_cost=" . $tier1_price . " WHERE tier=\"1\"";
	    $update_tier2 = "UPDATE AdvertCost SET tier_cost=" . $tier2_price . " WHERE tier=\"2\"";
	    $update_tier3 = "UPDATE AdvertCost SET tier_cost=" . $tier3_price . " WHERE tier=\"3\"";
	    if(!empty($tier1_price)) {
		if(mysqli_query($conn,$update_tier1)) {
		    $adjust_tier_err = "Tier prices updated!";
		}
	    }
	    if(!empty($tier2_price)) {
                if(mysqli_query($conn,$update_tier2)) {
                    $adjust_tier_err = "Tier prices updated!";
                }
            }
	    if(!empty($tier3_price)) {
                if(mysqli_query($conn,$update_tier3)) {
                    $adjust_tier_err = "Tier prices updated";
                }
            }
	}
    }
    
    // get revenue statistics to fill modal content box
    $yearly_sql = "SELECT SUM(amount) AS year_revenue FROM Revenue WHERE date_paid >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
    $yearly_result = $conn->query($yearly_sql);
    if($yearly_result->num_rows > 0) {
	$year_row = $yearly_result->fetch_assoc();
	$yearly_revenue = $year_row["year_revenue"];
    } else {
	$yearly_revenue = "No revenue has been accumulated!";
    }
    
    // get revenue statistics to fill modal content box
    $monthly_sql = "SELECT SUM(amount) AS month_revenue FROM Revenue WHERE date_paid >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
    $monthly_result = $conn->query($monthly_sql);
    if($monthly_result->num_rows > 0) {
        $month_row = $monthly_result->fetch_assoc(); 
        $monthly_revenue = $month_row["month_revenue"];
    } else {
        $monthly_revenue = "No revenue has been accumulated!";
    }
    
    // get revenue statistics to fill modal content box
    $weekly_sql = "SELECT SUM(amount) AS week_revenue FROM Revenue WHERE date_paid >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
    $weekly_result = $conn->query($weekly_sql);
    if($weekly_result->num_rows > 0) {
        $week_row = $weekly_result->fetch_assoc(); 
        $weekly_revenue = $week_row["week_revenue"];
    } else {
        $weekly_revenue = "No revenue has been accumulated!";
    }



    // initalize variable to hold historical sales
    $history = "";
    $history_err="";
    // load up sale history
    $select = "SELECT * FROM Sales";
    $condition1history = " WHERE ending_sale_date<NOW()";
    $sql = $select . $condition1_history;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
	    $col_name = "<td>" . "<a href=\"Sale.php?sale=" . $row["sale_id"] . "&view=v" . "\">" .  $row["sale_name"] . "</a>" .  "</td>";
	    $col_location =  "<td>" . $row["sale_resNum"] . " " . $row["sale_street"] . " " . $row["sale_city"]. ", " . $row["sale_state"] . " " . $row["sale_zip"] . "</td>";
	    $sale_profit = "<td>" . $row["sale_total_profit"] . "</td>";
	    $s_date_obj = new DateTime($row["starting_sale_date"]);
	    $e_date_obj = new DateTime($row["ending_sale_date"]);
	    $s_date_info = $s_date_obj->format('m-d-Y H:i');
            $e_date_info = $e_date_obj->format('m-d-Y H:i');
	    $col_date = "<td>" . $s_date_info . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $e_date_info . "</td>";
	    $sale_history_entry = $col_name . $col_location . $col_date;
	    // Allow admin to delete any sale no matter the accrued balance on that sale
	    $delete_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
            $delete_id = "<input type=\"hidden\" name=\"delete_sale_id\" value=\"" . $row["sale_id"] . "\">";
            $delete_action = "<input type=\"hidden\" name=\"action\" value=\"delete_sale\">";
	    $sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"history\">";
            $delete_submit = "<input type=\"submit\" value=\"Delete\">";
            $delete_form_close = "</form>";
	    $col_delete = "<td>Balance: $" . $row["balance"] . $delete_form . $delete_id . $delete_action . $sale_type . $delete_submit . $delete_form_close . "</td>";
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
    $condition1_upcoming = " WHERE ending_sale_date>NOW()";
    $sql = $select . $condition1_upcoming;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $col_name = "<td>" . "<a href=\"Sale.php?sale=" . $row["sale_id"] . "&view=m" .  "\">" .  $row["sale_name"] . "</a>" .  "</td>";
            $col_location =  "<td>" . $row["sale_resNum"] . " " . $row["sale_street"] . " " . $row["sale_city"]. ", " . $row["sale_state"] . " " . $row["sale_zip"] . "</td>";
            $sale_profit = "<td>" . $row["sale_total_profit"] . "</td>";
	    $s_date_obj = new DateTime($row["starting_sale_date"]);
            $e_date_obj = new DateTime($row["ending_sale_date"]);
            $s_date_info = $s_date_obj->format('m-d-Y H:i');
            $e_date_info = $e_date_obj->format('m-d-Y H:i');
            $col_date = "<td>" . $s_date_info . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $e_date_info . "</td>";
            $delete_sale_sql = "DELETE FROM Sales WHERE sale_id=\"" . row["sale_id"] . "\"";
	    // if user has paid their oustanding balance allow them to delete the sale
            $delete_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
            $delete_id = "<input type=\"hidden\" name=\"delete_sale_id\" value=\"" . $row["sale_id"] . "\">";
            $delete_action = "<input type=\"hidden\" name=\"action\" value=\"delete_sale\">";
	    $sale_type = "<input type=\"hidden\" name=\"sale_type\" value=\"upcoming\">";
            $delete_submit = "<input type=\"submit\" value=\"Delete\">";
            $delete_form_close = "</form>";
            $col_delete = "<td>Balance: $" . $row["balance"] . $delete_form . $delete_id . $delete_action . $sale_type . $delete_submit . $post_sale . $delete_form_close . "</td>";
            $upcoming_sales_entry = $col_name . $col_location . $col_date . $sale_profit .  $col_delete;
            $upcoming_sales .= "<tr>" .  $upcoming_sales_entry . "</tr>";
        }
    } else {
        $upcoming_sales_err = "No Upcoming Sales!";
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
         <h4 class="w3-center">Hello <?php echo $_SESSION['username']; ?>!</h4>
         <p class="w3-center"><img src="images/avatar.png" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
         <hr>
         <p><i class="fa fa-pencil fa-fw w3-margin-right w3-text-theme"></i>YSC Admin</p>
        </div>
      </div>
      <br>

      <!-- Accordion -->
      <div class="w3-card w3-round">
        <div class="w3-white">
	  <h4 class="w3-center" style="padding-top:8px">Sale Hub</h4>
	  <p style="text-align:center"><?=$adjust_tier_err?></p>
          <button style="border:8px solid black" data-toggle="modal" data-target="#AdjustTier" class="w3-button w3-block w3-theme-l1 w3-left-align"><i style="color:black" class="fa fa-circle-o-notch fa-fw w3-margin-right"></i>Adjust Promo Tier Prices</button>
          <button style="border:8px solid black" data-toggle="modal" data-target="#ViewRevenue" class="w3-button w3-block w3-theme-l1 w3-left-align"><i style="color:black" class="fa fa-circle-o-notch fa-fw w3-margin-right"></i>View Revenue</button>
            <div class="modal fade" id="AdjustTier" role="dialog">
              <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content" style="overflow:scroll;height:500px">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Adjust Promo Tier Prices</h4>
                </div>
                <div class="modal-body">
                  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		    <p style="text-align:center">Input prices up to 99 dollars and exclude dollar sign</p>
		    <p style="text-align:center">You can leave values blank if you dont want them changed</p>
		    <p id="formtag">Tier 1</p>
  		    <input id="input" type="text" size="2" name="tier1"  maxlength="2">
		    <p id="formtag">Tier 2</p>
                    <input id="input" type="text" size="2" name="tier2"  maxlength="2">
		    <p id="formtag">Tier 3</p>
                    <input id="input" type="text" size="2" name="tier3"  maxlength="2">
		    <input id="input" type="hidden" name="action" value="update_tier_cost">  
		    <br><br><input type="submit" value="Adjust Price">
		    <input type="reset" value="Reset">
		  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

	      </div>
	      </div>
            </div>
            <div class="modal fade" id="ViewRevenue" role="dialog">
              <div class="modal-dialog">

              <!-- Modal content-->
              <div class="modal-content" style="overflow:scroll;height:500px">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Revenue Statistics</h4>
                </div>
                <div class="modal-body">
                  <h1 style="text-align:center">Revenue for past year</h1>
		  <p style="color:green;text-align:center">$<?=$yearly_revenue?></p>
		  <h1 style="text-align:center">Revenue for past month</h1>
                  <p style="color:green;text-align:center">$<?=$monthly_revenue?></p>
  		  <h1 style="text-align:center">Revenue for past week</h1>
                  <p style="color:green;text-align:center">$<?=$weekly_revenue?></p>
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
      <div style="height:450px;overflow:scroll" class="w3-container w3-card w3-white w3-round w3-margin"><br>
        <h2 style="text-decoration:underline;text-align:center">Sale History</h2>
	<p style="text-align:center">Click Sale Title to Manage Sale</p>
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
      <div style="height:450px;overflow:scroll" class="w3-container w3-card w3-white w3-round w3-margin"><br>
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
