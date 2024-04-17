<?php
	
	/*
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	*
	* Retrieves the real IP address of the client
	* Prioritizes HTTP_CLIENT_IP, then HTTP_X_FORWARDED_FOR, and finally REMOTE_ADDR
	* 
	*/

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

	$real_ip_address = "";

	if (isset($_SERVER['HTTP_CLIENT_IP']))
	{
		$real_ip_address = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$real_ip_address = trim($ips[0]);
	}
	elseif (isset($_SERVER['REMOTE_ADDR']))
	{
		$real_ip_address = $_SERVER['REMOTE_ADDR'];
	}

?>
