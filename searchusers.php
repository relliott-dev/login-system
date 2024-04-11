<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/11
	* 
	* This script is responsible for fetching user data from the database based on search criteria, pagination settings, and sorting parameters
	* It checks if a user is logged in and has admin privileges before proceeding with data retrieval
	* The script returns the user data in JSON format, including information about total pages and the current page for front-end pagination controls
	* 
	*/

	session_start();

	include('config.php');

	if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin')
	{
		http_response_code(403);
		echo json_encode(['success' => false, 'message' => 'Access denied.']);
		exit;
	}

	$search = $_GET['search'] ?? '';
	$page = (int)($_GET['page'] ?? 1);
	$perPage = 20;
	$offset = ($page - 1) * $perPage;
	$sort = in_array($_GET['sort'], ['username', 'email', 'banned'], true) ? $_GET['sort'] : 'username';
	$order = $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

	$searchTerm = "%$search%";
	$query = "SELECT * FROM user_accounts WHERE username LIKE ? OR email LIKE ? ORDER BY $sort $order LIMIT ? OFFSET ?";
	$stmt = $db->prepare($query);
	$stmt->bind_param("ssii", $searchTerm, $searchTerm, $perPage, $offset);
	$stmt->execute();
	$result = $stmt->get_result();
	$users = $result->fetch_all(MYSQLI_ASSOC);

	$totalQuery = "SELECT COUNT(*) as total FROM user_accounts WHERE username LIKE ? OR email LIKE ?";
	$totalStmt = $db->prepare($totalQuery);
	$totalStmt->bind_param("ss", $searchTerm, $searchTerm);
	$totalStmt->execute();
	$totalResult = $totalStmt->get_result();
	$totalRow = $totalResult->fetch_assoc();
	$totalPages = ceil($totalRow['total'] / $perPage);

	echo json_encode([
		'users' => $users,
		'totalPages' => $totalPages,
		'currentPage' => $page,
	]);

?>
