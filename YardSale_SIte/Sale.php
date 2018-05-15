<?php
    require_once('accessDB.php');
    session_start();
    $username = "";
    $item_id = "";
    $item_results = "";

    if(isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        $username = $_SESSION['username'];
	$permissions = $_SESSION['permissions'];
        if($permissions == 'u') {
            $dashboard = "dashboard.php";
        } else if($permissions == 'm') {
            $dashboard = "AdminDashboard.php";
        }
	$greeting = "Welcome " . $username;
    } else {
        $greeting = "Welcome Visitor!";
    }

    if($_SERVER["REQUEST_METHOD"] == 'GET' || $_SERVER["REQUEST_METHOD"] == 'POST') {
	if($_SERVER["REQUEST_METHOD"] == 'GET') {
	    $sale_id = $_GET["sale"];
	    $view = $_GET["view"];
	} else {
	    $sale_id = $_POST["sale_id"];
	    $type = $_POST["type"];
	    $view = $_POST["view"];
	}
    } else {
   	header('location: homepage.php');
    }

    $update_item_err = "";
    $delete_item_err = "";
    $time_err = "";
    $tier_err = "";
    $item_err = "";
    if(isset($_POST) && !empty($_POST)) {
        if($type == "time") {
            $new_s_date = $_POST["s_sale_year"] . "-" . $_POST["s_sale_month"] . "-" . $_POST["s_sale_day"] . " " . $_POST["s_sale_hour"] . ":" . $_POST["s_sale_minute"] . ":00";
            $new_e_date = $_POST["e_sale_year"] . "-" . $_POST["e_sale_month"] . "-" . $_POST["e_sale_day"] . " " . $_POST["e_sale_hour"] . ":" . $_POST["s_sale_minute"] . ":00";
            $new_e_obj = new DateTime($new_e_date);
	    $new_s_obj = new DateTime($new_s_date);
            $current_date = date('Y-m-d H:i:s');
            $current_obj = new DateTime($current_date);
            if($new_e_obj < $current_obj) {
                $time_err = "Time NOT Updated!  The updated ending date has already passed!";
            } else if($new_e_obj < $new_s_obj) {
	    	$time_err = "Time NOT Updated! The new ending date is less than the new start date!";
	    } else {
                $time_update_sql = "UPDATE Sales SET starting_sale_date=\"" . $new_s_date . "\"" . ",ending_sale_date=\"" . $new_e_date . "\"" . "WHERE sale_id=\"" . $sale_id . "\"";
                if(mysqli_query($conn, $time_update_sql)) {
                    $time_err = "Time has been updated!";
                } else {
                    $time_err = "Error Updating Time: " . mysqli_error($conn);
                }
            }
	} else if($type == "tier") {
	    // run code to process promo tier update
	    if(($_POST["tier"] > $_POST["new_tier"]) && $_POST["tier"] != "h") {
		$to_query = $_POST["new_tier"];
		$tier_update_sql = "UPDATE Sales SET promo_tier=\"" . $to_query . "\"" . "WHERE sale_id=\"" . $sale_id . "\"";
	 	if(mysqli_query($conn, $tier_update_sql)) {
		    $tier_err = "Tier has been updated!";
		    $sql_get_cost = "SELECT tier_cost FROM AdvertCost WHERE tier=" . $_POST["new_tier"];
		    // TOOOOOODODODODODODODOODD
		    $new_cost_row = $conn->query($sql_get_cost);
		    if($new_cost_row->num_rows > 0) {
			$new_cost_result = $new_cost_row->fetch_assoc();
			$new_balance = (int)$new_cost_result["tier_cost"] - $_POST["old_ad_cost"]; 
			$sql_update_cost = "UPDATE Sales SET ad_cost=" . $new_cost_result["tier_cost"] . ",balance=" . $new_balance . " WHERE sale_id=" . $sale_id;
			if(mysqli_query($conn, $sql_update_cost)) {
		            $tier_err = "Tier and payment balance updated!";
			} else {
			    $tier_err = "Error Updating Sale Cost Associated With New Tier: " .  mysqli_error($conn);
			}
		    } else {
			$tier_err = "Error Updating Sale Payment Balance: ";
		    }
		} else {
		    $tier_err = "Error Updating Tier: " . mysqli_error($conn);
	        }
	    } else if($_POST["new_tier"] == "h") {
                $to_query = $_POST["new_tier"];
                $tier_update_sql = "UPDATE Sales SET promo_tier=\"" . $to_query . "\"" . "WHERE sale_id=\"" . $sale_id . "\"";
                if(mysqli_query($conn, $tier_update_sql)) {
		    $tier_err = "Sale has been hidden and will remain unsearchable until changed";
		} else {
		    $tier_err = "Error switching to hidden mode: " . mysqli_error($conn);
		}
	    } else {
		if($_POST["tier"] == "h") { 
		    $tier_err = "Sorry this sale has been hidden by the administrator.  Contact support for assistance.";
		} else {
		    $tier_err = "You cannot update your sale to a lower tier once you've upgraded!";
		}
	    }
	} else if($type == "delete_item") {
	    $delete_item_sql = "DELETE FROM Items WHERE item_id=\"" . $_POST["delete_id"] . "\"";
	    if(mysqli_query($conn, $delete_item_sql)) {
		$delete_item_err = "Item has been deleted!";
	    } else {
		$delete_item_err = "Error Deleting Item: " . mysqli_error($conn);
	    }
	} else if($type == "update_item") {
	    $update_spec = $_POST["avail"];
	    if($update_spec == "sold") {
		$update_to = 0;
	    } else {
		$update_to = 1;
	    }
	    $update_item_sql = "UPDATE Items SET sold=\"" . $update_to . "\"" . "WHERE item_id=\"" . $_POST["to_update"] . "\"";
	    if(mysqli_query($conn, $update_item_sql)) {
		if($update_to == 0) {
		    $update_sale_profit = "UPDATE Sales SET sale_total_profit=sale_total_profit +" . doubleval($_POST["update_profit"]) . ' ' . "WHERE sale_id=\"" . $sale_id . "\"";
		} else {
		    $update_sale_profit = "UPDATE Sales SET sale_total_profit=sale_total_profit -" . doubleval($_POST["update_profit"]) . " " . "WHERE sale_id=\"" . $sale_id . "\"";
		}
		if(mysqli_query($conn, $update_sale_profit)) {
		    $update_item_err = "Item Availability successfully updated!";
		} else {
		    $update_item_err = "Error Updating Sale Profit: " . $mysqli_error($conn);
		}
	    } else {
		$update_item_err = "Error Updating Item: " . $mysqli_error($conn);
	    }
        } else if($type == "add_item") {
	    // add item action
	    $name = $_POST["new_item_name"];
	    $category = $_POST["new_item_category"];
	    $price = $_POST["new_item_price"];
	    $price = doubleval($price);
	    number_format($price, 2, '.', '');
	    if(is_numeric($price)) {
		$item_insert_sql = "INSERT INTO Items (sale_id, item_name, category, price, sold) VALUES (" . $sale_id . "," . "\"" .  $name . "\"" .  "," . "\"" .  $category . "\"" . "," . $price . ", 1)";
	        if(mysqli_query($conn, $item_insert_sql)) {
		    $item_err = "Item has been added to this sale!";
	        } else {
		    $item_err = "Error Adding Item: " . mysqli_error($conn) . "with statement" . $item_insert_sql;
		}
	    } else {
		$item_err = "Invalid price...item NOT added!";
	    }
	} else {
	}
    }


    // get sale information
    $sql_sale = "SELECT * FROM Sales WHERE sale_id=\"" . $sale_id . "\"";
    $sale_info_row = $conn->query($sql_sale);
    if($sale_info_row->num_rows > 0) {
	$sale_info = $sale_info_row->fetch_assoc();
	// extract sale info
	$sale_name = $sale_info["sale_name"];
	$sale_contact = $sale_info["sale_contact"];
	$sale_location = $sale_info["sale_resNum"] . " " . $sale_info["sale_street"] . " " . $sale_info["sale_city"] . ", " . $sale_info["sale_state"] . " " . $sale_info["sale_zip"];
	$sale_ad_cost = (int)$sale_info["ad_cost"];
	$s_date_obj = new DateTime($sale_info["starting_sale_date"]);
	$e_date_obj = new DateTime($sale_info["ending_sale_date"]);
	$start_date = $s_date_obj->format('m-d-Y H:i');
	$end_date = $e_date_obj->format('m-d-Y H:i');
	$tier = $sale_info["promo_tier"];
    }

    // set tier update for management view

    // find items within the sale
    $select = "SELECT * FROM Items";
    $condition1 = " WHERE sale_id=\"" . $sale_id  . "\"";
    $sql_items = $select . $condition1;
    $result = $conn->query($sql_items);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $col_name = "<td>" . $row["item_name"]  . "</td>"; 
            $col_category =  "<td>" . $row["category"] . "</td>";
            $col_price = "<td>" . "$" .  $row["price"] .  "</td>";

	    // if host view show option to update item availability and delete button
	    if($view == "m") {
	    	$form_open = "<form id=\"updateItem\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
            	$select_open = "<select id=\"avail\" onchange=\"this.form.submit()\" name=\"avail\">"; 
	    	if($row["sold"] == 1) {
		    $option_sold = "<option value=\"sold\">Sold</option>";
		    $option_avail = "<option value=\"not_sold\" selected=\"selected\">Available</option>";
	    	} else {
		    $option_sold = "<option value=\"sold\" selected=\"selected\">Sold</option>";
                    $option_avail = "<option value=\"not_sold\">Available</option>";
	    	}
		$select_close = "</select>";
		$update_id = "<input type=\"hidden\" name=\"to_update\" value=\"" . $row["item_id"] . "\">";
		$post_sale = "<input type=\"hidden\" name=\"sale_id\" value=\"" . $sale_id . "\">";
		$post_view = "<input type=\"hidden\" name=\"view\" value=\"" . $view . "\">";
		$update_item_type = "<input type=\"hidden\" name=\"type\" value=\"update_item\">";
		$update_submit = "<input type=\"submit\" style=\"display:none\" value=\"submit\">";
		$update_profit = "<input type=\"hidden\" name=\"update_profit\" value=\"" . $row["price"] . "\">"; 
		$form_close = "</form>";
		$availability = $form_open . $select_open . $option_sold . $option_avail . $select_close . $update_id . $post_sale . $post_view . $update_profit . $update_item_type . $update_submit . $form_close;
		if($view == "m") {
		    $delete_form = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
		    $delete_id = "<input type=\"hidden\" name=\"delete_id\" value=\"" . $row["item_id"] . "\">";
		    $delete_item_type = "<input type=\"hidden\" name=\"type\" value=\"delete_item\">";
		    $delete_submit = "<input type=\"submit\" value=\"Delete\">";
		    $delete_form_close = "</form>";
		    $col_manage = "<td>" . $delete_form . $delete_id . $delete_item_type . $delete_submit . $post_sale . $post_view . $delete_form_close . "</td>";
		} 
	    } else {
		if($row["sold"] == 1) {
		    $availability = "Available";
		} else {
		    $availability = "Sold";
		}
	    }
            $col_avail = "<td>" . $availability . "</td>";
            $item_results_entry = $col_name . $col_category . $col_price . $col_avail;
	    if($view == "m") {$item_results_entry .= $col_manage;}
            $item_results .= "<tr>" .  $item_results_entry . "</tr>";
	}
    } else {
	$item_results = "<p style=\"font-weight:bold;text-align:center\">No items exist for this sale!</p>";
    }


?>


<!DOCTYPE html>
<html>
<title>YSC Sale</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>

#avail {
	width:auto;
}

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
	width: 800px;
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


#formtag { margin-bottom:0px}
body,h1,h2{font-family: "Raleway", sans-serif}
body, html {height: 100%}
p {line-height: 2}
.bgimg, .bgimg2 {
    min-height: 100%;
    background-position: center;
    background-size: cover;
}

.bgimg {background-image: url("/images/HomeBG.jpg")}
</style>
<body>

<!-- Navbar (top) -->
<div class="w3-top w3-hide-small">
  <div class="w3-bar w3-white w3-center w3-padding w3-hover-opacity-off">
    <a style="width:auto;font-weight:bold;font-size:15px" class="w3-bar-item"><?=$greeting?></a>
    <?php if(!empty($_SESSION['username'])) {
	    echo "<a href=\"" . $dashboard . "\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Dashboard</a>";
	    echo "<a href=\"logout.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Logout</a>";
	} else {
	    echo "<a href=\"loginPage.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Login</a>";
            echo "<a href=\"registerPage.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Register</a>";
    	}
    ?>
  </div>
</div>


<!-- Sale Listing Pane -->
<div style="height=100%" class="w3-container w3-pale-red bgimg2">
  <div>
    <div class="w3-container w3-round w3-margin"><br>
      <h1 style="padding-top:3%;text-decoration:underline;text-align:center"><?php if(!empty($sale_name)) {echo $sale_name;}?></h1>
      <p><?=$dump?></p>
      <?php if($view == "v") { echo "<p style=\"text-align:center\"><button style=\"font-weight:bold;border:2px solid black;text-align:center\"type=\"button\" class=\"w3-button\" onclick=\"window.history.go(-1); return false;\">Back to Results</button></p>";} ?>
      <p style="text-align:center;margin-top:-10px;font-size:18px"><?php if(!empty($sale_location)) {echo $sale_location;}?></p>
      <p style="text-align:center;margin-top:-20px;font-size:18px"><?php if(!empty($start_date) && !empty($end_date)) {echo $start_date . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $end_date;}?></p>
      <p style="text-align:center;margin-top:-10px;font-size:18px">Contact Number: <?php if(!empty($sale_contact)) {echo $sale_contact;}?></p>
      <?php if($view == "m") {
	        echo "<div style=\"text-align:center;margin-top:-10px\">";
	        echo "Promotional-Tier: " . $tier . " ";
	        echo "<form style=\"display:inline\" method=\"post\" action=\"" . $_SERVER["PHP_SELF"] . "\">";
	        echo "<select name=\"new_tier\">";
	        echo "<option value=\"1\">1</option>";
	        echo "<option value=\"2\">2</option>";
	        echo "<option value=\"3\">3</option>";
	        if($permissions == 'm') { 
		    echo "<option value=\"h\">hide</option>";
		}
		echo "</select>";
		echo "<input type=\"hidden\" value=\"" . $tier  . "\"" . "name=\"tier\">";
		echo "<input type=\"hidden\" value=\"" . $sale_id . "\"" . "name=\"sale_id\">";
		echo "<input type=\"hidden\" value=\"" . $view . "\"" . "name=\"view\">";
		echo "<input type=\"hidden\" value=\"" . $sale_ad_cost . "\"" . "name=\"old_ad_cost\">"; 
		echo "<input type=\"hidden\" value=\"tier\"" . "name=\"type\">";
	        echo "<input type=\"submit\" value=\"Update Promo-Tier\">";
	        echo "</form>";
		echo "<p>" .  $tier_err . "</p>";
	        echo "</div>";
		echo "<div style=\"text-align:center;padding-top:10px\">";
		echo "<button style=\"border:3px solid black\" onclick=\"document.getElementById('UpdateTime').style.display='block'\"class=\"w3-button\">Click to Modify Start or End Date</button><br>";
		echo "<p>" . $time_err . "</p>";
		echo "<button style=\"border:3px solid black;margin-top:8px\" onclick=\"document.getElementById('AddItem').style.display='block'\"class=\"w3-button\">Click to Add Item to Sale</button>";
		echo "<p>" . $item_err . "</p>";
		echo "</div>";
		echo "<p style=\"text-align:center\">" . $update_item_err . "</p>";
		echo "<p style=\"text-align:center\">" . $delete_item_err . "</p>";
	    }
      ?>


      <!-- The Modal -->
      <div id="UpdateTime" class="w3-modal">
        <div class="w3-modal-content">
        <div class="w3-container">
          <span onclick="document.getElementById('UpdateTime').style.display='none'"class="w3-button w3-display-topright">&times;</span>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	    <p id="formtag">Starting-Day/Month/Year: </p>
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
            <input id="input" type="text" size="4" name="s_sale_year" minlength="4" maxlength="4" required>
            <p style="display:inline" id="formtag">Starting-Hour/Minute: </p>
            <input id="input" size="2" type="text" size="2" name="s_sale_hour" min="1" max="12" minlength="2" required><span> : </span>
            <input id="input" size="2" type="text" size="2" name="s_sale_minute" min="0" max="59" minlength="2" required>
	    <p id="formtag">Ending-Day/Month/Year: </p>
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
            <input id="input" type="text" size="4" name="e_sale_year" minlength="4" maxlength="4" required>
            <p style="display:inline" id="formtag">Ending-Hour/Minute: </p>
            <input id="input" size="2" type="text" size="2" name="e_sale_hour" min="1" max="12" minlength="2" required><span> : </span>
            <input id="input" size="2" type="text" size="2" name="e_sale_minute" min="0" max="59" minlength="2" required>
	    <input type="hidden" value="time" name = "type">
	    <input type="hidden" value="<?php echo $view; ?>" name = "view">
	    <input type="hidden" value="<?php echo $sale_id; ?>" name = "sale_id">
	    <br><br><input type="submit" value="Submit">
            <input type="reset" value="Reset"><br>
	  </form>
        </div>
        </div>
      </div>

      <!-- The Modal -->
      <div id="AddItem" class="w3-modal">
        <div class="w3-modal-content">
        <div class="w3-container">
          <span onclick="document.getElementById('AddItem').style.display='none'"class="w3-button w3-display-topright">&times;</span>
          <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	    <p id="formtag">Item Name: </p>
	    <input id="input" type="text" name="new_item_name" required><br>
	    <p id="formtag">Item Category: </p>
	    <select name="new_item_category" required>
              <option value="Furniture">Furniture</option>
              <option value="Appliances">Appliances</option>
              <option value="Automotive">Automotive</option>
              <option value="Sports Gear">Sports Gear</option>
              <option value="Electronics">Electronics</option>
              <option value="Kitchenware">Kitchenware</option>
              <option value="Toys">Toys</option>
              <option value="Clothing">Clothing</option>
              <option value="Outdoor Gear">Outdoor Gear</option>
              <option value="Misc">Misc</option>
            </select><br>
	    <p id="formtag">Item Price (exclude dollar sign): </p>
	    <input id="input" type="text" name="new_item_price" required>
	    <input type="hidden" value="add_item" name = "type">
            <input type="hidden" value="<?php echo $view; ?>" name = "view">
            <input type="hidden" value="<?php echo $sale_id; ?>" name = "sale_id">
            <br><br><input type="submit" value="Submit">
            <input type="reset" value="Reset"><br>
          </form>
        </div>
        </div>
      </div>

      <div>
        <div>
	  <table class="center-block">
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Sale Price</th>
	        <th>Availability</th>
		<?php if($view == "m") { echo "<th>Manage</th>";} ?>
              </tr>
            </thead>
            <tbody>
	      <?php echo $item_results; ?>
            </tbody>
          </table>
	</div>
      </div>

  </div>
</div>

</body>
</html>


