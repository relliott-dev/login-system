<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/11
	* 
	* This PHP script is designed to fetch audit log entries for a specific user
	* The user's username is received via a GET request and used to query the database
	* The script also handles pagination and returns the result as JSON
	* 
	*/

	session_start();

	include('config.php');

	if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	$username = $_GET['username'] ?? '';
	$page = $_GET['page'] ?? 1;
	$perPage = 20;
	$offset = ($page - 1) * $perPage;

	$totalQuery = "SELECT COUNT(*) as total FROM audit_logs
 		JOIN user_accounts ON audit_logs.userid = user_accounts.userid
		WHERE user_accounts.username = ?";
	$totalStmt = $db->prepare($totalQuery);
	$totalStmt->bind_param("s", $username);
	$totalStmt->execute();
	$totalResult = $totalStmt->get_result();
	$totalRow = $totalResult->fetch_assoc();
	$totalEntries = $totalRow['total'];

	$query = "SELECT audit_logs.* FROM audit_logs
		JOIN user_accounts ON audit_logs.userid = user_accounts.userid
		WHERE user_accounts.username = ? ORDER BY audit_logs.timestamp DESC
		LIMIT ? OFFSET ?";
	$stmt = $db->prepare($query);
	$stmt->bind_param("sii", $username, $perPage, $offset);
	$stmt->execute();
	$result = $stmt->get_result();

	$auditLog = [];
	while ($row = $result->fetch_assoc())
	{
		$auditLog[] = $row;
	}

	echo json_encode(['auditLog' => $auditLog, 'totalEntries' => $totalEntries]);

?>
