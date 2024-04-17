<?php

	/*
	* Author: Russell Elliott
	* Date Created: 2024/04/02
	* 
	* This function is used to log audit events in the database
	* It records the user ID, action taken, a description of the action, and the IP address of the user
	* 
	*/

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	function logAudit($db, $userId, $action, $description, $ip)
	{
		$query = "INSERT INTO audit_logs (userid, action, description, ip) VALUES (?, ?, ?, ?)";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "isss", $userId, $action, $description, $ip);
		mysqli_stmt_execute($stmt);
	}

?>
