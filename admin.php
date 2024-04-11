<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This script serves as the admin page for managing user accounts
	* It provides functionalities such as searching, sorting, and paginating user accounts, as well as displaying statistics about user registrations and status
	* Only users with the 'admin' role can access this page
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

	include('config.php');
	include('sessionmanager.php');

	session_start();

	if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin')
	{
		header("Location: login.php");
		exit;
	}

	$search = $_GET['search'] ?? '';
	$page = $_GET['page'] ?? 1;
	$perPage = 20;
	$offset = ($page - 1) * $perPage;
	$sort = $_GET['sort'] ?? 'username';
	$order = $_GET['order'] ?? 'asc';

	$allowedSort = ['username', 'email', 'banned'];
	$allowedOrder = ['asc', 'desc'];
	$sort = in_array($sort, $allowedSort) ? $sort : 'username';
	$order = in_array($order, $allowedOrder) ? $order : 'asc';

	$query = "SELECT * FROM user_accounts WHERE username LIKE ? OR email LIKE ? ORDER BY $sort $order LIMIT ? OFFSET ?";
	$stmt = $db->prepare($query);
	$searchTerm = "%$search%";
	$stmt->bind_param("ssii", $searchTerm, $searchTerm, $perPage, $offset);
	$stmt->execute();
	$result = $stmt->get_result();

	$users = $result->fetch_all(MYSQLI_ASSOC);

	$totalQuery = "SELECT COUNT(*) as total FROM user_accounts WHERE username LIKE ? OR email LIKE ?";
	$totalStmt = $db->prepare($totalQuery);
	$totalStmt->bind_param("ss", $searchTerm, $searchTerm);
	$totalStmt->execute();
	$totalResult = $totalStmt->get_result();
	$totalRow = $totalResult->fetch_assoc();
	$totalPages = ceil($totalRow['total'] / $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="UTF-8">
	<title>Admin Page</title>
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
		<h1>Admin Page</h1>

		<div id="registrationChart" style="height: 400px; min-width: 310px"></div>
		<div id="bannedChart" style="height: 400px; min-width: 310px"></div>

		<div class="table-controls">
			<input type="text" id="searchBox" placeholder="Search users..." onkeyup="searchUsers()">
		</div>
		<div class="table-responsive">
			<table id="usersTable" class="table">
				<thead>
					<tr>
						<th><a href="#" onclick="sortUsers('username')">Username</a></th>
						<th><a href="#" onclick="sortUsers('email')">Email</a></th>
						<th><a href="#" onclick="sortUsers('banned')">Status</a></th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<!-- User data will be loaded here via JavaScript -->
				</tbody>
			</table>
		</div>

		<div id="pagination-container" class="pagination">
			<?php

				if ($page > 1)
				{
					echo '<a href="?page=' . ($page - 1) . '&search=' . urlencode($search) . '">&laquo; Previous</a>';
				}

				$start = max($page - $range, 1);
				$end = min($page + $range, $totalPages);

				for ($i = $start; $i <= $end; $i++)
				{
					if ($i == $page)
					{
						echo '<span class="active">' . $i . '</span>';
					}
					else
					{
						echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a>';
					}
				}

				if ($page < $totalPages)
				{
					echo '<a href="?page=' . ($page + 1) . '&search=' . urlencode($search) . '">Next &raquo;</a>';
				}

			?>
		</div>

		<div class="table-spacing"></div>

		<div class="table-responsive" id="auditLogContainer">
			<h2>Audit Log</h2>

			<table id="auditLogTable" class="table">
				<thead>
					<tr>
						<th>Timestamp</th>
						<th>Action</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<!-- Audit log entries will be loaded here via JavaScript -->
				</tbody>
			</table>
		</div>

		<div id="auditLogPagination" class="pagination">
			<!-- Pagination will be loaded here via JavaScript -->
		</div>
	</div>

	<?php include('footer.php'); ?>

	<script>

	function updateTable(users)
	{
		console.log(users);
		const tableBody = document.getElementById("usersTable").getElementsByTagName('tbody')[0];
		tableBody.innerHTML = "";

		users.forEach(user =>
		{
			let row = tableBody.insertRow();
			row.insertCell(0).innerText = user.username;
			row.insertCell(1).innerText = user.email;
			row.insertCell(2).innerText = user.banned ? 'Banned' : 'Active';

			let actionCell = row.insertCell(3);
			let actionButton = document.createElement('button');
			actionButton.innerText = user.banned ? 'Unban' : 'Ban';

			actionButton.onclick = function(event)
			{
				event.stopPropagation();
				toggleBan(user.username, parseInt(user.banned));
			};

			actionCell.appendChild(actionButton);

			row.addEventListener('click', function()
			{
				let selectedRows = document.querySelectorAll('.selected-row');

				selectedRows.forEach(function(selectedRow)
				{
					selectedRow.classList.remove('selected-row');
				});

				row.classList.add('selected-row');
				fetchUserInformation(user.username);
			});
		});
	}

	let selectedUsername = '';

	function fetchUserInformation(username, page = 1)
	{
		selectedUsername = username;
		fetch(`auditlog.php?username=${encodeURIComponent(username)}&page=${page}`)
		.then(response =>
		{
			if (!response.ok)
			{
				throw new Error(`HTTP error! status: ${response.status}`);
			}
                	return response.json();
		})
		.then(data =>
		{
			const auditLogTableBody = document.getElementById("auditLogTable").getElementsByTagName('tbody')[0];
			auditLogTableBody.innerHTML = "";

			data.auditLog.forEach(entry =>
			{
				let row = auditLogTableBody.insertRow();
				row.insertCell(0).innerText = entry.timestamp;
				row.insertCell(1).innerText = entry.action;
				row.insertCell(2).innerText = entry.description;
			});

			setupAuditLogPagination(data.totalEntries, page);
		})
		.catch(error =>
		{
			console.error('Fetch error:', error);
		});
	}

	function setupPagination(totalPages, currentPage, sort, order, search)
	{
		const paginationContainer = document.getElementById("pagination-container");
		paginationContainer.innerHTML = "";

		for (let i = 1; i <= totalPages; i++)
		{
			let pageLink = document.createElement('a');
			pageLink.innerText = i;

			if (currentPage === i)
			{
				pageLink.classList.add('active');
			}
			else
			{
				pageLink.href = "#";
                    
				pageLink.addEventListener('click', (e) =>
				{
					e.preventDefault();
					searchUsers(i, sort, order, search);
				});
			}

			paginationContainer.appendChild(pageLink);
		}
	}

	function setupAuditLogPagination(totalEntries, currentPage)
	{
		const paginationContainer = document.getElementById("auditLogPagination");
		paginationContainer.innerHTML = "";
		const totalPages = Math.ceil(totalEntries / 20);

		for (let i = 1; i <= totalPages; i++)
		{
			let pageLink = document.createElement('a');
			pageLink.innerText = i;

			if (currentPage === i)
			{
				pageLink.classList.add('active');
			}
			else
			{
				pageLink.href = "#";

				pageLink.addEventListener('click', (e) =>
				{
					e.preventDefault();
					fetchUserInformation(selectedUsername, i);
				});
			}

			paginationContainer.appendChild(pageLink);
		}
	}

	let currentSortColumn = 'username';
	let currentSortOrder = 'asc';

	function sortUsers(column)
	{
		if (column === currentSortColumn)
		{
			currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
		}
		else
		{
			currentSortOrder = 'asc';
		}

		currentSortColumn = column;

		searchUsers(1, currentSortColumn, currentSortOrder);
	}

	function searchUsers(page = 1, sort = currentSortColumn, order = currentSortOrder, search = '')
	{
		if (!search)
		{
			search = document.getElementById("searchBox").value;
		}

		const url = `searchusers.php?page=${page}&sort=${sort}&order=${order}&search=${encodeURIComponent(search)}`;
		console.log("Requesting data from:", url);

		fetch(url)
		.then(response =>
		{
			if (!response.ok)
			{
				throw new Error(`HTTP error! status: ${response.status}`);
			}

                	return response.json();
		})
		.then(data =>
		{
			console.log("Data received:", data);
			updateTable(data.users);
			setupPagination(data.totalPages, data.currentPage, data.sort, data.order, data.search);
		})
		.catch(error =>
		{
			console.error('Fetch error:', error);
		});
	}

	function toggleBan(username, isBanned)
	{
		console.log(`Toggling ban status for ${username}, isBanned: ${isBanned}`);
		const banStatus = isBanned ? 0 : 1;

		fetch('toggleban.php',
		{
			method: 'POST',
			headers:
			{
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({ username: username, banStatus: banStatus })
		})
		.then(response => response.json())
		.then(data =>
		{
			if (data.success)
			{
				searchUsers();
			}
			else
			{
				alert('Failed to update ban status: ' + data.message);
			}
		})
		.catch(error =>
		{
			console.error('Error:', error);
		});
	}

	function updateRegistrationChart(timeRange)
	{
		const endDate = new Date();
		let startDate;

		switch (timeRange)
		{
			case '1m':
				startDate = new Date(endDate.getFullYear(), endDate.getMonth() - 1, endDate.getDate());
				break;
			case '3m':
				startDate = new Date(endDate.getFullYear(), endDate.getMonth() - 3, endDate.getDate());
				break;
			case '6m':
				startDate = new Date(endDate.getFullYear(), endDate.getMonth() - 6, endDate.getDate());
				break;
			case '1y':
				startDate = new Date(endDate.getFullYear() - 1, endDate.getMonth(), endDate.getDate());
				break;
			case '5y':
				startDate = new Date(endDate.getFullYear() - 5, endDate.getMonth(), endDate.getDate());
				break;
			default:
				startDate = new Date(endDate.getFullYear() - 5, endDate.getMonth(), endDate.getDate());
		}

		fetch(`logindata.php?start=${startDate.toISOString().split('T')[0]}&end=${endDate.toISOString().split('T')[0]}`)
		.then(response => response.json())
		.then(data =>
		{
			registrationChart.data.labels = data.registration.labels;
			registrationChart.data.datasets[0].data = data.registration.data;
			registrationChart.update();
		})
		.catch(error =>
		{
			console.error('Fetch error:', error);
		});
	}

	function renderCharts()
	{
		fetch('logindata.php')
		.then(response =>
		{
			if (!response.ok)
			{
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return response.json();
		})
		.then(data =>
		{
			const registrationSeries = data.registration.labels.map((date, index) =>
			{
				return [new Date(date).getTime(), data.registration.data[index]];
			});

			Highcharts.stockChart('registrationChart',
			{
				rangeSelector:
				{
					selected: 1,
					buttons:
					[
						{ type: 'month', count: 1, text: '1m' },
						{ type: 'month', count: 3, text: '3m' },
						{ type: 'month', count: 6, text: '6m' },
						{ type: 'year', count: 1, text: '1y' },
						{ type: 'year', count: 5, text: '5y' }
					]
				},
				title: { text: 'Daily Registrations' },
				series: [{ name: 'Registrations', data: registrationSeries, tooltip: { valueDecimals: 0 } }]
			});

			Highcharts.chart('bannedChart',
			{
				chart: { type: 'bar' },
				title: { text: 'User Status' },
				xAxis: { categories: ['Banned Status', 'Active Status'] },
				yAxis: { title: { text: 'Number of Users' } },
				series:
				[{
					name: 'Banned',
					data: [parseInt(data.banned.Banned), null]
				},
				{
					name: 'Unbanned',
					data: [parseInt(data.banned.Unbanned), null]
				},
				{
					name: 'Active',
					data: [null, parseInt(data.active.Active)]
				},
				{
					name: 'Inactive',
					data: [null, parseInt(data.active.Inactive)]
				}]
			});
		})
		.catch(error =>
		{	
			console.error('Fetch error:', error);
		});
	}

	document.addEventListener('DOMContentLoaded', function()
	{
		searchUsers();
		renderCharts();
	});

	setInterval(function()
	{
		window.location.reload();
	}, 300000);

	</script>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://code.highcharts.com/stock/highstock.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>

</body>
</html>
