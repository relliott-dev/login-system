<?php

	/*
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This script handles user login functionality
	* It checks if the user exists in the database, verifies the password, and updates the user's last login IP and time
	* If the login is successful, the user is redirected to the index page
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

	include('config.php');
	include('sessionmanager.php');
	include('auditlogger.php');
	include("getip.php");
	
	$error_msg = '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$username = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
		$password = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
		
		if (empty($username) || empty($password))
		{
			$error_msg = "Please fill all required fields";
		}
		else
		{
			$query = "SELECT * FROM user_accounts WHERE username = ? OR email = ?";
			$loginStmt = mysqli_prepare($db, $query);
			mysqli_stmt_bind_param($loginStmt, "ss", $username, $username);
			mysqli_stmt_execute($loginStmt);
			$result = mysqli_stmt_get_result($loginStmt);
			$getInfo = mysqli_fetch_array($result);
			
			if ($getInfo)
			{
				if ($getInfo['banned'] == 1)
				{
					logAudit($db, $getInfo['userid'], 'Login Attempt', 'Account is banned', $real_ip_address);
					$error_msg = "Your account has been banned";
				}
				elseif (password_verify($password, $getInfo['password']))
				{
					$query = "UPDATE user_accounts SET ip = ?, last_login = NOW() WHERE userid = ?";
					$updateIPStmt = mysqli_prepare($db, $query);
					mysqli_stmt_bind_param($updateIPStmt, "si", $real_ip_address, $getInfo["userid"]);
					$updateIPResult = mysqli_stmt_execute($updateIPStmt);
                    
					if ($updateIPResult)
					{
						if ($getInfo["active"] == 1)
						{
							logAudit($db, $getInfo['userid'], 'Login Success', 'User logged in successfully', $real_ip_address);
							$_SESSION['username'] = $getInfo['username'];
							$_SESSION['role'] = $getInfo['role'];
							$_SESSION['banned'] = $getInfo['banned'];
							echo "<script>window.location.href = 'index.php';</script>";
						}
						else
						{
							logAudit($db, $getInfo['userid'], 'Login Attempt', 'Inactive account', $real_ip_address);
							$error_msg = "Inactive Account";
						}
					}
					else
					{
						$error_msg = "Could not update IP";
					}
				}
				else
				{
					logAudit($db, $getInfo['userid'], 'Login Attempt', 'Invalid credentials', $real_ip_address);
					$error_msg = "Invalid username/email or password";
				}
			}
			else
			{
				logAudit($db, null, 'Login Attempt', 'Account not found', $real_ip_address);
				$error_msg = "Could not find account";
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
	<meta charset="UTF-8">
	<title>Login Form</title>
	<meta name="description" content="A short description of the page's content.">
	<meta name="keywords" content="keyword1, keyword2, keyword3">
	<meta name="author" content="Author's Name">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
    
</head>
<body>

	<?php include('navbar.php'); ?>
    
	<div class="container">
	<h1>User Login</h1>
        
	<?php if (!empty($error_msg)) { echo '<div class="alert alert-danger" role="alert">' . $error_msg . '</div>'; } ?>

	<form action="login.php" method="post">
		<div class="form-group">
			<label for="username">Username/Email:</label>
			<input type="text" id="username" name="username" class="form-control"><br>
		</div>
		<div class="form-group">
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" class="form-control"><br>
		</div>
		<button type="submit" class="btn btn-primary">Login</button>
	</form>

	<p><a href="forgotpassword.php">Forgot Password?</a></p>

	</div>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
