<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/11
	* 
	* This script checks the current session for a valid admin user
	* If the session is invalid or the user is not an admin, it prepares a response to trigger a logout
	* The 'isAdmin' function is defined to check the user's role and banned status in the database
	* 
	*/

	session_start();

	include('config.php');

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	$response = ['logout' => false, 'message' => 'User is okay.'];

	if (!isset($_SESSION['username']))
	{
		$response['logout'] = true;
		$response['message'] = 'Session not found.';
	}
	else
	{
		$username = $_SESSION['username'];
		if (!isAdmin($db, $username))
		{
			$response['logout'] = true;
			$response['message'] = 'User is banned or not admin.';
		}
	}

	echo json_encode($response);

	function isAdmin($db, $username)
	{
		$query = "SELECT role, banned FROM user_accounts WHERE username = ?";
		$stmt = $db->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		$userInfo = $result->fetch_assoc();
		return $userInfo && $userInfo['role'] === 'admin' && $userInfo['banned'] === 0;
	}

?>
