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

	if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

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
			<!-- Pagination will be loaded here via JavaScript -->
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

	let selectedUsername = '';
	let currentSortColumn = 'username';
	let currentSortOrder = 'asc';

	function updateTable(users)
	{
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

		if (currentPage > 1)
		{
			let firstPageLink = document.createElement('a');
			firstPageLink.innerText = 'First';
			firstPageLink.href = "#";
			
			firstPageLink.addEventListener('click', (e) =>
			{
				e.preventDefault();
				searchUsers(1, sort, order, search);
			});
			
			paginationContainer.appendChild(firstPageLink);
		}

		for (let i = Math.max(1, currentPage - 4); i <= Math.min(totalPages, currentPage + 4); i++)
		{
			let pageLink = document.createElement('a');
			pageLink.innerText = i;

			if (currentPage === i)
			{
				pageLink.classList.add('active');
				pageLink.removeAttribute('href');
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

		if (currentPage < totalPages)
		{
			let lastPageLink = document.createElement('a');
			lastPageLink.innerText = 'Last';
			lastPageLink.href = "#";
			lastPageLink.addEventListener('click', (e) =>
			{
				e.preventDefault();
				searchUsers(totalPages, sort, order, search);
			});
		
			paginationContainer.appendChild(lastPageLink);
		}
	}

	function setupAuditLogPagination(totalEntries, currentPage)
	{
		const paginationContainer = document.getElementById("auditLogPagination");
		paginationContainer.innerHTML = "";
		const totalPages = Math.ceil(totalEntries / 20);

		if (currentPage > 1)
		{
			let firstPageLink = document.createElement('a');
			firstPageLink.innerText = 'First';
			firstPageLink.href = "#";

			firstPageLink.addEventListener('click', (e) =>
			{
				e.preventDefault();
				fetchUserInformation(selectedUsername, 1);
			});

			paginationContainer.appendChild(firstPageLink);
		}

		for (let i = Math.max(1, currentPage - 4); i <= Math.min(totalPages, currentPage + 4); i++)
		{
			let pageLink = document.createElement('a');
			pageLink.innerText = i;

			if (currentPage === i)
			{
				pageLink.classList.add('active');
				pageLink.removeAttribute('href');
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

		if (currentPage < totalPages)
		{
			let lastPageLink = document.createElement('a');
			lastPageLink.innerText = 'Last';
			lastPageLink.href = "#";

			lastPageLink.addEventListener('click', (e) =>
			{
				e.preventDefault();
				fetchUserInformation(selectedUsername, totalPages);
			});

			paginationContainer.appendChild(lastPageLink);
		}
	}

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
