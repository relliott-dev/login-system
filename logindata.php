<?php

	session_start();

	include('config.php');

	$endDate = new DateTime();
	$startDate = (new DateTime())->modify('-5 years');

	$period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate);

	$registrationData = [];
	foreach ($period as $date)
	{
		$registrationData[$date->format('Y-m-d')] = 0;
	}

	$registrationQuery = "SELECT DATE(created_at) AS date, COUNT(*) AS count FROM user_accounts WHERE created_at >= ? GROUP BY DATE(created_at) ORDER BY DATE(created_at)";
	$stmt = $db->prepare($registrationQuery);
	$stmt->bind_param("s", $startDate->format('Y-m-d'));
	$stmt->execute();
	$registrationResult = $stmt->get_result();

	while ($row = $registrationResult->fetch_assoc())
	{
		$registrationData[$row['date']] = (int)$row['count'];
	}

	$bannedQuery = "SELECT banned, COUNT(*) AS count FROM user_accounts GROUP BY banned";
	$bannedResult = $db->query($bannedQuery);

	$bannedData = ['Banned' => 0, 'Unbanned' => 0];

	while ($row = $bannedResult->fetch_assoc())
	{
		if ($row['banned'] == 1)
		{
			$bannedData['Banned'] = $row['count'];
		}
		else
		{
			$bannedData['Unbanned'] = $row['count'];
		}
	}
    
	$activeQuery = "SELECT active, COUNT(*) AS count FROM user_accounts GROUP BY active";
	$activeResult = $db->query($activeQuery);

	$activeData = ['Active' => 0, 'Inactive' => 0];

	while ($row = $activeResult->fetch_assoc())
	{
		if ($row['active'] == 1)
		{
			$activeData['Active'] = $row['count'];
		}
		else
		{
			$activeData['Inactive'] = $row['count'];
		}
	}

	echo json_encode([
		'registration' =>
		[
            		'labels' => array_keys($registrationData),
            		'data' => array_values($registrationData)
		],
		'banned' => $bannedData,
		'active' => $activeData
	]);

?>
