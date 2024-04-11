<?php

	session_start();

	include('config.php');

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
