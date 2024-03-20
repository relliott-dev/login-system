<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This PHP script is used to access the database for querying
  * 
  */

	$host = "localhost"; // Default host for MySQL
	$port = "3306";      // Default port for MySQL
	$user = "username";  // Your MySQL username
	$pass = "password";  // Your MySQL password
	$dbase = "database_name"; // Your database name

	// Connection string
	$db = mysqli_connect("$host:$port", $user, $pass, $dbase) or die("Error " . mysqli_error($db));

	// Set the timezone
	date_default_timezone_set("America/Chicago");

?>
