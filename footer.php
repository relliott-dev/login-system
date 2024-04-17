<?php
	
	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This file is used to include a consistent footer across all pages
	* It includes the current date and website name
	* 
	*/

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

?>

<footer class="bg-success text-white text-center p-3 fixed-bottom">
	<p>&copy; <?php echo date("Y"); ?> Your Website Name. All Rights Reserved.</p>
</footer>
