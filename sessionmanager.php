<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/11
	* 
	* This PHP script checks if the current session's user is an administrator and not banned
	* If the user is not an admin or is banned, they are redirected to the logout page
	* It also updates the 'LAST_ACTIVITY' session variable to track user activity for session timeouts
	* 
	*/

	session_start();

	include('config.php');

	if (isset($_SESSION['username']) && !isAdmin($db, $_SESSION['username']))
	{
		header('Location: logout.php');
		exit;
	}

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

	$_SESSION['LAST_ACTIVITY'] = time();

?>

<script>
    
	let timeout;

	function resetTimeout()
	{
		clearTimeout(timeout);
		timeout = setTimeout(() =>
		{
			window.location.href = 'logout.php';
		}, 180000);
	}

	resetTimeout();

	document.addEventListener('mousemove', resetTimeout);
	document.addEventListener('keypress', resetTimeout);

	setInterval(() =>
	{
		fetch('checkstatus.php')
		.then(response => response.json())
		.then(data =>
		{
			if (data.logout)
			{
				window.location.href = 'logout.php';
			}
		})
		.catch(error => console.error('Error:', error));
	}, 10000);

</script>
