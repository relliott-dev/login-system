<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/04/02
	* 
	* This script serves as the template for the user's profile page
	* It begins a new session or continues the current one and includes a responsive navigation bar and footer
	* This page is intended to display and allow editing of user-specific information
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

	session_start();

	include('config.php');
	include('auditlogger.php');
	include('getip.php');

	if (!isset($_SESSION['username']))
	{
		header("Location: login.php");
	}

	$username = $_SESSION['username'];

	$userIdQuery = "SELECT userid FROM user_accounts WHERE username = ?";
	$userIdStmt = mysqli_prepare($db, $userIdQuery);
	mysqli_stmt_bind_param($userIdStmt, "s", $username);
	mysqli_stmt_execute($userIdStmt);
	$userIdResult = mysqli_stmt_get_result($userIdStmt);
	$userRow = mysqli_fetch_assoc($userIdResult);
	$userId = $userRow['userid'];

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$firstName = mysqli_real_escape_string($db, $_POST['firstName']);
		$lastName = mysqli_real_escape_string($db, $_POST['lastName']);
		$gender = mysqli_real_escape_string($db, $_POST['gender']);
		$dateOfBirth = mysqli_real_escape_string($db, $_POST['dateOfBirth']);
		$streetAddress = mysqli_real_escape_string($db, $_POST['streetAddress']);
		$city = mysqli_real_escape_string($db, $_POST['city']);
		$state = mysqli_real_escape_string($db, $_POST['state']);
		$country = mysqli_real_escape_string($db, $_POST['country']);
		$zipCode = mysqli_real_escape_string($db, $_POST['zipCode']);
		$phoneNumber = mysqli_real_escape_string($db, $_POST['phoneNumber']);
		$aboutMe = mysqli_real_escape_string($db, $_POST['aboutMe']);

		$checkQuery = "SELECT userid FROM user_profiles WHERE userid = ?";
		$checkStmt = mysqli_prepare($db, $checkQuery);
		mysqli_stmt_bind_param($checkStmt, "i", $userId);
		mysqli_stmt_execute($checkStmt);
		$checkResult = mysqli_stmt_get_result($checkStmt);

		if (mysqli_num_rows($checkResult) > 0)
		{
			logAudit($db, $userId, 'Profile Updated', 'User profile updated successfully', $real_ip_address);
			$updateQuery = "UPDATE user_profiles SET first_name = ?, last_name = ?, gender = ?, date_of_birth = ?, street_address = ?, city = ?, state = ?, country = ?, zip_code = ?, phone_number = ?, about_me = ? WHERE userid = ?";
			$updateStmt = mysqli_prepare($db, $updateQuery);
			mysqli_stmt_bind_param($updateStmt, "sssssssssssi", $firstName, $lastName, $gender, $dateOfBirth, $streetAddress, $city, $state, $country, $zipCode, $phoneNumber, $aboutMe, $userId);
			mysqli_stmt_execute($updateStmt);
		}
		else
		{
			logAudit($db, $userId, 'Profile Created', 'User profile created successfully', $real_ip_address);
			$insertQuery = "INSERT INTO user_profiles (userid, first_name, last_name, gender, date_of_birth, street_address, city, state, country, zip_code, phone_number, about_me) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$insertStmt = mysqli_prepare($db, $insertQuery);
			mysqli_stmt_bind_param($insertStmt, "isssssssssss", $userId, $firstName, $lastName, $gender, $dateOfBirth, $streetAddress, $city, $state, $country, $zipCode, $phoneNumber, $aboutMe);
			mysqli_stmt_execute($insertStmt);
		}
	}

	$query = "SELECT * FROM user_accounts INNER JOIN user_profiles ON user_accounts.userid = user_profiles.userid WHERE username = ?";
	$stmt = mysqli_prepare($db, $query);
	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$userInfo = mysqli_fetch_assoc($result);

	if (!$userInfo)
	{
		$userInfo = [
			'username' => $username,
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'gender' => '',
			'date_of_birth' => '',
			'street_address' => '',
			'city' => '',
			'state' => '',
			'country' => '',
			'zip_code' => '',
			'phone_number' => '',
			'about_me' => ''
		];
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<title>User Profile</title>
	<meta name="description" content="A short description of the page's content.">
	<meta name="keywords" content="keyword1, keyword2, keyword3">
	<meta name="author" content="Author's Name">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">

</head>
<body>

	<?php include('navbar.php'); ?>

	<div class="container">

	<h2>Edit Profile</h2>
	<form action="profile.php" method="post">
		<div class="form-group">
			<label for="firstName">First Name:</label>
			<input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userInfo['first_name']); ?>">
		</div>
		<div class="form-group">
			<label for="lastName">Last Name:</label>
			<input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userInfo['last_name']); ?>">
		</div>
		<div class="form-group">
			<label for="gender">Gender:</label>
			<select class="form-control" id="gender" name="gender">
				<option value="Male" <?php echo $userInfo['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
				<option value="Female" <?php echo $userInfo['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
				<option value="Other" <?php echo $userInfo['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
			</select>
		</div>
		<div class="form-group">
			<label for="dateOfBirth">Date of Birth:</label>
			<input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($userInfo['date_of_birth']); ?>">
		</div>
		<div class="form-group">
			<label for="streetAddress">Street Address:</label>
			<input type="text" class="form-control" id="streetAddress" name="streetAddress" value="<?php echo htmlspecialchars($userInfo['street_address']); ?>">
		</div>
		<div class="form-group">
			<label for="city">City:</label>
			<input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($userInfo['city']); ?>">
		</div>
		<div class="form-group">
			<label for="state">State:</label>
			<input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($userInfo['state']); ?>">
		</div>
		<div class="form-group">
			<label for="country">Country:</label>
			<input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($userInfo['country']); ?>">
		</div>
		<div class="form-group">
			<label for="postalCode">Zip Code:</label>
			<input type="text" class="form-control" id="zipCode" name="zipCode" value="<?php echo htmlspecialchars($userInfo['zip_code']); ?>">
		</div>
		<div class="form-group">
			<label for="phoneNumber">Phone Number:</label>
			<input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($userInfo['phone_number']); ?>">
		</div>
		<div class="form-group">
			<label for="aboutMe">About Me:</label>
			<textarea class="form-control" id="aboutMe" name="aboutMe"><?php echo htmlspecialchars($userInfo['about_me']); ?></textarea>
		</div>
		<button type="submit" class="btn btn-primary">Update Profile</button>
		</form>
	</div>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
