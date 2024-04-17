<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/02
	* 
	* This PHP script handles password reset functionality for users
	* It checks user-provided reset codes against stored values in the database, updates the user's password upon successful code verification, and then removes the reset code from the database
	* The script employs prepared statements for database interactions, ensuring security against SQL injection, and applies password hashing for stored passwords
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

	session_start();

	include("config.php");
	include('auditlogger.php');
	include("getip.php");

	$error_msg = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$code = isset($_POST['code']) ? trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING)) : '';
		$email = isset($_POST['email']) ? trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) : '';
		$password = isset($_POST['password']) ? trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING)) : '';
		$confirmpassword = isset($_POST['confirmpassword']) ? trim(filter_input(INPUT_POST, 'confirmpassword', FILTER_SANITIZE_STRING)) : '';

		if (empty($code) || empty($email) || empty($password) || empty($confirmpassword))
		{
			$error_msg = "Please fill all required fields";
		}
		else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$error_msg = "Invalid email format";
		}
		else if (!ctype_digit($code))
		{
			$error_msg = "Code must contain only numbers";
		}
		else if ($password != $confirmpassword)
		{
			$error_msg = "The password and confirm password must match";
		}
		else
		{
			$query = "SELECT userid FROM forgot_passwords WHERE code = ? AND email = ?";
			$stmt = mysqli_prepare($db, $query);
			mysqli_stmt_bind_param($stmt, "ss", $code, $email);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if (mysqli_num_rows($result) > 0)
			{
				$userInfo = mysqli_fetch_array($result);
				$userId = $userInfo['userid'];

				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
				$updateQuery = "UPDATE users SET password = ? WHERE email = ?";
				$updateStmt = mysqli_prepare($db, $updateQuery);
				mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $email);
				mysqli_stmt_execute($updateStmt);

				if (mysqli_stmt_affected_rows($updateStmt) > 0)
				{
					$deleteQuery = "DELETE FROM forgot_passwords WHERE email = ?";
					$deleteStmt = mysqli_prepare($db, $deleteQuery);
					mysqli_stmt_bind_param($deleteStmt, "s", $email);
					mysqli_stmt_execute($deleteStmt);
					
					if (mysqli_stmt_affected_rows($deleteStmt) > 0)
					{
						logAudit($db, $userId, 'Password Reset', 'User successfully reset password', $real_ip_address);
						echo "<script>window.location.href = 'login.php';</script>";
					}
					else
					{
						$error_msg = "Could not remove from reset passwords table";
					}
				}
				else
				{
					$error_msg = "Could not update password";
				}
			}
			else
			{
				logAudit($db, null, 'Invalid Reset Code', 'User attempted password reset with an invalid code', $real_ip_address);
				$error_msg = "The activation code is invalid";
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
	<meta charset="UTF-8">
	<title>Reset Password</title>
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
	<h2>Reset Password</h2>

	<?php if (!empty($error_msg)) { echo '<div class="alert alert-danger" role="alert">' . $error_msg . '</div>'; } ?>

	<form action="resetpassword.php" method="post">
		<div class="form-group">
			<label for="email">Email:</label>
			<input type="text" class="form-control" id="email" name="email">
		</div>
		<div class="form-group">
			<label for="code">Reset Code:</label>
			<input type="text" class="form-control" id="code" name="code">
		</div>
		<div class="form-group">
			<label for="password">New Password:</label>
			<input type="password" class="form-control" id="password" name="password">
		</div>
		<div class="form-group">
			<label for="confirmpassword">Confirm New Password:</label>
			<input type="password" class="form-control" id="confirmpassword" name="confirmpassword">
		</div>
		<button type="submit" class="btn btn-primary" name="reset_password">Reset Password</button>
	</form>

	</div>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
