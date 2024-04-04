<?php

	/*
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This script manages the user registration process
	* It checks for empty fields and validates that the password and confirm password match
	* It ensures the username and email are not already in use and inserts the new user record into the database
	* Successful registration redirects the user to the home page
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

	include('config.php');
	include('sessionmanager.php');
	include('auditlogger.php');
	include('activationmail.php');
	include("getip.php");

	$error_msg = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$username = isset($_POST['username']) ? filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) : '';
		$email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
		$password = isset($_POST['password']) ? filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) : '';
		$confirmpassword = isset($_POST['confirmpassword']) ? filter_input(INPUT_POST, 'confirmpassword', FILTER_SANITIZE_STRING) : '';
		$mac = isset($_POST['mac']) ? filter_input(INPUT_POST, 'mac', FILTER_SANITIZE_STRING) : '';

		if (empty($username) || empty($email) || empty($password) || empty($confirmpassword))
		{
			$error_msg = "Please fill all required fields";
		}
		elseif ($password != $confirmpassword)
		{
			$error_msg = "The password and confirm password must match";
		}
		else
		{
			$query = "SELECT * FROM user_accounts WHERE username = ? OR email = ?";
			$stmt = mysqli_prepare($db, $query);
			mysqli_stmt_bind_param($stmt, "ss", $username, $email);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if (mysqli_num_rows($result) > 0)		
			{
				while ($row = mysqli_fetch_assoc($result))
				{
					if ($row['username'] == $username)
					{
						logAudit($db, null, 'Registration Attempt', 'Username already in use: ' . $username, $real_ip_address);
						$error_msg = "The username is already in use";
					}
					elseif ($row['email'] == $email)
					{
						logAudit($db, null, 'Registration Attempt', 'Email already in use: ' . $email, $real_ip_address);
						$error_msg = "The email is already in use";
					}
				}
			}
			else
			{
				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
				$query = "INSERT INTO user_accounts (email, username, password, active, mac, ip) VALUES (?, ?, ?, 1, ?, ?)";
				$registerStmt = mysqli_prepare($db, $query);
				mysqli_stmt_bind_param($registerStmt, "sssss", $email, $username, $hashedPassword, $mac, $real_ip_address);
				$result = mysqli_stmt_execute($registerStmt);

				if ($result)
				{
					logAudit($db, mysqli_insert_id($db), 'Registration Success', 'User registered successfully', $real_ip_address);
					echo "<script>window.location.href = 'index.php';</script>";
				}
				else
				{
					$error_msg = "Could not register user";
				}
			}
		}
	}
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
	<meta charset="UTF-8">
	<title>Registration Form</title>
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
		<h1>User Registration</h1>
        
		<?php if (!empty($error_msg)) { echo '<div class="alert alert-danger" role="alert">' . $error_msg . '</div>'; } ?>
        
		<form action="register.php" method="post" class="mt-3">
        		<div class="form-group">
			    	<label for="username">Username:</label>
				<input type="text" id="username" name="username" class="form-control">
        		</div>
        		<div class="form-group">
			    	<label for="email">Email:</label>		
				<input type="email" id="email" name="email" class="form-control">
			</div>
			<div class="form-group">
				<label for="password">Password:</label>
				<input type="password" id="password" name="password" class="form-control">
			</div>
			<div class="form-group">
				<label for="confirmpassword">Confirm Password:</label>
				<input type="password" id="confirmpassword" name="confirmpassword" class="form-control">
			</div>
			<button type="submit" class="btn btn-primary">Register</button>
		</form>
	</div>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
