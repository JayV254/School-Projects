<?php

    // Include DB connect file

    require_once 'accessDB.php';

    // Define variables and initialize with empty values

    $username = $password = "";
    $username_err = $password_err = "";
    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        // Check if username is empty
	if(empty(trim($_POST["username"]))){

            $username_err = 'Please enter username.';

        } else {

            $username = trim($_POST["username"]);
	}

        // Check if password is empty
	if(empty(trim($_POST['password']))){
	    $password_err = 'Please enter your password.';

        } else{
	    $password = trim($_POST['password']);
	}

        // Validate credentials
	if(empty($username_err) && empty($password_err)){

            // Prepare a select statement
            $sql = "SELECT username, password, permissions FROM Users WHERE username = ?";
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
		mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameters
		$param_username = $username;

                // Attempt to execute the prepared statement

                if(mysqli_stmt_execute($stmt)){

                    // Store result

                    mysqli_stmt_store_result($stmt);

                    // Check if username exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1) {
                        // Bind result variables
			mysqli_stmt_bind_result($stmt, $username, $hashed_password, $permissions);
			if(mysqli_stmt_fetch($stmt)) {

                            if(password_verify($password, $hashed_password)){

                                /* Password is correct, so start a new session and

                                save the username to the session */
				session_start();

                                $_SESSION['username'] = $username;
				$_SESSION['permissions'] = $permissions;
				if($_SESSION['permissions'] == 'm') {
				    header("location: AdminDashboard.php");
				} else {
				    header("location: dashboard.php");
				}

                            } else{

                                // Display an error message if password is not valid
				$password_err = 'The password you entered was not valid.';
			    }

                        }

                    } else{

                        // Display an error message if username doesn't exist
			$username_err = 'No account found with that username.';
		    }

                } else{

                    echo "Oops! Something went wrong. Please try again later.";

                }

            } else {
		echo "SQL error";
	    }
            // Close statement

            mysqli_stmt_close($stmt);

        }

        // Close connection
	mysqli_close($link);
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Login</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body {
	    font: 14px sans-serif;
	    background-image: url("/images/HomeBG.jpg");
	    background-repeat:no-repeat;
	    background-size:cover;
	}
    </style>
</head>

<body>
    <div class="container" style="padding-left:10%; padding-top:15%">
    <div class="col-md-5 col-md-offset-5">
	<center style="font-weight:bold;font-family:courier;font-size:250%;color:white">LOGIN</center>
	<center style="padding-bottom:15px;padding-top:10px;font-family:courier;font-size:145%;color:white">Please fill in your credentials</center>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
		<label style="font-family:courier;font-size:200%;color:white">Username:</label>
		<input type="text" name="username"class="form-control" value="<?php echo $username; ?>">
		<span class="help-block"><?php echo $username_err; ?></span>
	    </div>

            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
		<label style="font-family:courier;font-size:200%;color:white">Password:</label>
		<input type="password" name="password" class="form-control">
		<span class="help-block"><?php echo $password_err; ?></span>
	    </div>
	    <div class="form-group">
		<input type="reset" class="btn btn-default" value="Reset">
                <input type="submit" class="btn btn-primary" style="float:right" value="Submit">
	    </div>
	    <center style="font-family:courier;font-size:145%;color:white;font-weight:bold;">No Account? <a style="color:#5bc0de" href="registerPage.php">Sign up now</a></center>
	</form>
        </div>
    </div>
</body>
</html>



