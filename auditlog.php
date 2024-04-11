<?php

	session_start();

	include('config.php');

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
