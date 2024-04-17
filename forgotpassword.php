<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/02
	* 
	* This PHP script manages the forgot password functionality for users
	* It sends a reset code to the user's email if it exists in the database
	* The script uses prepared statements for database interactions to ensure security against SQL injection
	* It also includes validation of the email format and handles different response scenario
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/
	
	include("config.php");
	include('auditlogger.php');
	include('activationmail.php');
	include("getip.php");
	
	$error_msg = '';
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{        
		$email = isset($_POST['email']) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '';
		
		if (empty($email))
		{
			$error_msg = "Please fill all required fields";
		}
		else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$error_msg = "Invalid email format";
		}
		else
		{
			$query = "SELECT * FROM user_accounts WHERE email = ?";
			$stmt = mysqli_prepare($db, $query);
			mysqli_stmt_bind_param($stmt, "s", $email);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
            
			if (mysqli_num_rows($result) > 0)
			{
				$accountInfo = mysqli_fetch_array($result);
				$userId = $accountInfo['userid'];
				
				logAudit($db, $userId, 'Password Reset Request', 'User requested a password reset', $real_ip_address);
				
				$code = rand(100000, 999999);
				
				$query = "SELECT * FROM forgot_passwords WHERE userid = ?";
				$checkStmt = mysqli_prepare($db, $query);
				mysqli_stmt_bind_param($checkStmt, "i", $userId);
				mysqli_stmt_execute($checkStmt);
				$checkResult = mysqli_stmt_get_result($checkStmt);
				
				if (mysqli_num_rows($checkResult) > 0)
				{
					$query = "UPDATE forgot_passwords SET code = ? WHERE userid = ?";
					$updateCodeStmt = mysqli_prepare($db, $query);
					mysqli_stmt_bind_param($updateCodeStmt, "si", $code, $userId);
					mysqli_stmt_execute($updateCodeStmt);
				}
				else
				{
					$query = "INSERT INTO forgot_passwords (userid, code, email) VALUES (?, ?, ?)";
					$insertStmt = mysqli_prepare($db, $query);
					mysqli_stmt_bind_param($insertStmt, "iss", $userId, $code, $email);
					mysqli_stmt_execute($insertStmt);
				}
				
				if (smtpmailer($email, 'Reset the Password', $code))
				{
					logAudit($db, $userId, 'Password Reset Code Sent', 'Reset code sent to user\'s email', $real_ip_address);
					echo "<script>window.location.href = 'resetpassword.php';</script>";
				}
				else
				{
					logAudit($db, $userId, 'Mail Server Failure', 'Failed to connect to the mail server', $real_ip_address);
					$error_msg = "Mail Server failed to connect";
				}
			}
			else
			{
				logAudit($db, null, 'Unregistered Email', 'Password reset attempted with an unregistered email', $real_ip_address);
				$error_msg = "The email is not registered";
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<title>Forgot Password</title>
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
	<h2>Forgot Password</h2>

	<?php if (!empty($error_msg)) { echo '<div class="alert alert-danger" role="alert">' . $error_msg . '</div>'; } ?>

	<form action="forgotpassword.php" method="post">
		<div class="form-group">
			<label for="email">Email:</label>
			<input type="email" class="form-control" id="email" name="email">
		</div>
		<button type="submit" class="btn btn-primary" name="forgot_password">Submit</button>
	</form>
	
	</div>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
