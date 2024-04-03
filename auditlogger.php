<?php

	function logAudit($db, $userId, $action, $description, $ip)
	{
		$query = "INSERT INTO audit_logs (userid, action, description, ip) VALUES (?, ?, ?, ?)";
		$stmt = mysqli_prepare($db, $query);
		mysqli_stmt_bind_param($stmt, "isss", $userId, $action, $description, $ip);
		mysqli_stmt_execute($stmt);
	}

?>
