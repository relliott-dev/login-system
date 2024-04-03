<?php

	/*
	*
 	* Author: Russell Elliott
	* Date Created: 2024/03/20
	*
	* This script is responsible for terminating the user's session
	* It unsets all session variables, destroys the session, and then redirects
	* the user back to the home page (index.php)
	* 
	*/

	session_start();

	include('config.php');
	include('auditlogger.php');
	include("getip.php");

	if (isset($_SESSION['username']))
	{	
		$username = $_SESSION['username'];

		$query = "SELECT userid FROM user_accounts WHERE username = ?";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "s", $username);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$userInfo = mysqli_fetch_array($result);
		$userId = $userInfo ? $userInfo['userid'] : null;

		logAudit($db, $userId, 'Logout', 'User logged out', $real_ip_address);
	}

	session_unset();
	session_destroy();
	header("Location: index.php");

?>
