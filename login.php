<?php

    /*
    * 
    * Author: Russell Elliott
    * Date Created: 2024/03/20
    * 
    * This script processes user login requests
    * It verifies user credentials against the database and updates the user's IP address upon successful login
    * It also handles error messaging for login failures and redirects to the home page upon successful authentication
    * 
    */
    
    session_start();
    
	include('config.php');
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
			$query = "SELECT * FROM users WHERE username = ?";
			$loginStmt = mysqli_prepare($db, $query);
			mysqli_stmt_bind_param($loginStmt, "s", $username);
			mysqli_stmt_execute($loginStmt);
			$result = mysqli_stmt_get_result($loginStmt);
			$getInfo = mysqli_fetch_array($result);
	
			if ($getInfo && password_verify($password, $getInfo['password']))
			{
				$query = "UPDATE users SET ip = ? WHERE id = ?";
				$updateIPStmt = mysqli_prepare($db, $query);
				mysqli_stmt_bind_param($updateIPStmt, "si", $real_ip_address, $getInfo["id"]);
				$updateIPResult = mysqli_stmt_execute($updateIPStmt);
	
				if ($updateIPResult)
				{
					if ($getInfo["active"] == 1)
					{
					    $_SESSION['username'] = $getInfo['username'];
						echo "<script>window.location.href = 'index.php';</script>";
					}
					else
					{
						$error_msg = "Inactive Account";
					}
				}
				else
				{
					$error_msg = "Error: Could not update IP";
				}
			}
			else
			{
				$error_msg = "Invalid username or password";
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
    
    <h1>User Login</h1>
	
    
    <?php if (!empty($error_msg)) { echo '<p style="color: red;">' . $error_msg . '</p>'; } ?>
	
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br><br>

        <button type="submit">Login</button>
    </form>
    
    <?php include('footer.php'); ?>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
