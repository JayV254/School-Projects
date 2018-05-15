<?php
    ini_set('session.cache_limiter','public');
    session_cache_limiter(false);
    require_once('accessDB.php');
    // initialize error fields
    $search_error = "";
    $sql_result = "";

    session_start();
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

    if($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST)) {
	$sale_results = "";
	// search form has been submitted, get q and perform search
	$searchterm = $_POST['q'];
	$tosearch = $_POST['toSearch4'];
	if($tosearch == "Sales") {
	    if(is_numeric($searchterm) && strlen($searchterm) == 5) {
		$select = "SELECT * FROM Sales";
            	$where_order = " WHERE sale_zip=\"" . $searchterm . "\" AND ending_sale_date>NOW() AND promo_tier!=\"h\" ORDER BY promo_tier";
		$sql = $select . $where_order;

	    } else {
		$search_error = "Invalid zip code!  Please try again.";
	    }
	} else if($tosearch == "Items") {
	    $sql = "SELECT * FROM Sales S INNER JOIN Items I WHERE S.promo_tier!=\"h\" AND S.ending_sale_date>NOW() AND S.sale_id=I.sale_id AND item_name LIKE" . "\"%" . $searchterm . "%\"" . "ORDER BY promo_tier";
	}
    	$result = $conn->query($sql);
    	if($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
	        $col_name = "<td>" . "<a id=\"saletag\" href=\"Sale.php?sale=" . $row["sale_id"] . "&view=v" . "\">" .  $row["sale_name"] . "</a>" .  "</td>";
	    	$col_location =  "<td>" . $row["sale_resNum"] . " " . $row["sale_street"] . " " . $row["sale_city"]. ", " . $row["sale_state"] . " " . $row["sale_zip"] . "</td>";
	        $s_date_obj = new DateTime($row["starting_sale_date"]);
	        $e_date_obj = new DateTime($row["ending_sale_date"]);
	        $s_date_info = $s_date_obj->format('m-d-Y H:i');
                $e_date_info = $e_date_obj->format('m-d-Y H:i');
	        $col_date = "<td>" . $s_date_info . "<span style=\"font-weight:bold\"> <-TO-> </span>" . $e_date_info . "</td>";
	        $result_entry = $col_name . $col_location . $col_date;
		if($tosearch == "Items") {
                    $col_item_name = "<td>" . $row["item_name"] . "</td>";
                    $col_item_price = "<td>" . "$" . $row["price"] . "</td>";
		    $result_entry .= $col_item_name . $col_item_price;
		}
                $sale_results .= "<tr>" .  $result_entry . "</tr>";
            }
    	} else {
            $sale_results = "<p>No Sales Found!</p>";
    	}
    }

?>

<!DOCTYPE html>
<html>
<title>YSC Homepage</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script async src="https://production-assets.codepen.io/assets/embed/ei.js"></script>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<style>

strong {
	font-weight: bold; 
}

#saletag {
	color:blue;
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
	font-size:15px;
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
	font-size:15px;
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

<!-- Header / Home-->
<header class="w3-display-container w3-wide bgimg" id="home">
  <div style="padding-right:5%" class="w3-display-right w3-text-white w3-center">
    <h1 class="w3-jumbo">(YS Collective)</h1>
    <h2>The hub for all Sales that occur...</h2>
    <h2>In Yards<b></b></h2><br><br><br><br>
    <h3>Scroll Down to begin your search!</h3>
  </div>
</header>

<!-- Navbar (top) -->
<div class="w3-top w3-hide-small" style="opacity:0.8">
  <div class="w3-bar w3-white w3-center w3-padding w3-hover-opacity-off">
    <a style="width:auto;font-weight:bold;font-size:15px" class="w3-bar-item"><?=$greeting?></a>
    <?php if(!empty($_SESSION['username'])) {
	    echo "<a href=\"" . $dashboard . "\"style=\"width:10%\" class=\"w3-bar-item w3-button\">Dashboard</a>";
	    echo "<a href=\"logout.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Logout</a>";
	} else {
	    echo "<a href=\"loginPage.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Login</a>";
            echo "<a href=\"registerPage.php\" style=\"width:10%\" class=\"w3-bar-item w3-button\">Register</a>";
    	}
    ?>
  </div>
</div>

<!-- Search Pane -->
<div class="w3-container w3-pale-red bgimg2">
  <a name="results"></a>
  <div>
    <p style="font-size:25px;text-align:center">Search Sales by Zip Code, or by Item Keyword!</p>
    <form style="padding-top:20px;text-align:center" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>#results">
      <div>
        <select name="toSearch4">
  	  <option value="Sales">Zipcode</option>
	  <option value="Items">Item Keyword</option>
	</select>
        <input type="search" placeholder="Type Here..."  name="q" required>
        <input type="submit" value="Submit">
      </div>
    </form>
    <p style="text-align:center;color:red;font-size=15px"><?php echo "$search_error"; ?></p>
  </div>
  <!-- Results table -->
  <h2 style="text-decoration:underline;text-align:center">Upcoming/Current Sales</h2>
	<p style="text-align:center">Click Sale Title to View Sale Details and Items!</p>
        <table class="historyTable">
          <thead>
            <tr>
              <th>Sale Title</th>
              <th>Location</th>
              <th>Start Date - End Date</th>
	      <?php if($_SERVER['REQUEST_METHOD'] == "POST" && $tosearch == "Items") {
	                echo "<th>Item</th>";
		        echo "<th>Item Price</th>";
		    } ?>
            </tr>
          </thead>
          <tbody>
            <?php echo $sale_results; ?>
          </tbody>
        </table>
</div>


<!-- Footer -->
<footer class="w3-center w3-black w3-padding-16">
  <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" title="W3.CSS" target="_blank" class="w3-hover-text-green">w3.css</a></p>
</footer>
<div class="w3-hide-small" style="margin-bottom:32px"> </div>

</body>
</html>

