<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This PHP script is used to access the database for querying
 	* Fill out the database information required for username, password and database name
 	* 
  	*/

	$host = "localhost";
	$port = "3306";
	$user = "username";
	$pass = "password";
	$dbase = "database_name";

	$db = mysqli_connect("$host:$port", $user, $pass, $dbase) or die("Error " . mysqli_connect_error($db));
	date_default_timezone_set("America/Chicago");
    	mysqli_set_charset($db, 'utf8mb4');

?>
