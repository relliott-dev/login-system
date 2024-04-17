<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/11
	* 
	* This script is used to change the ban status of a user in the system
	* It accepts JSON input containing the username and new ban status
	* The script checks if the user is an admin before allowing changes to the ban status
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

	$data = json_decode(file_get_contents('php://input'), true);
	$username = $data['username'] ?? '';
	$banStatus = isset($data['banStatus']) ? (int)$data['banStatus'] : null;

	if ($username === '' || $banStatus === null)
	{
		echo json_encode(['success' => false, 'message' => 'Invalid request.']);
		exit;
	}

	$query = "UPDATE user_accounts SET banned = ? WHERE username = ?";
	$stmt = $db->prepare($query);
	$stmt->bind_param("is", $banStatus, $username);
	$result = $stmt->execute();

	if ($result)
	{
		echo json_encode(['success' => true, 'username' => $username, 'banned' => $banStatus]);
	}
	else
	{
		echo json_encode(['success' => false, 'message' => 'Failed to update ban status.']);
	}

?>
