<?php

	/*
	*
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This PHP script generates a responsive navigation bar and supports dynamic content based on the user's session state
	* The navigation bar includes a dropdown menu under 'Page 1' and a conditional display of user-related links (Profile/Logout or Sign Up/Login)
	* 
	*/

	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
	{
		http_response_code(403);
		header("Location: index.php");
		exit;
	}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
	<button class="navbar-toggler left-toggler" type="button">
		<span class="navbar-toggler-icon"></span>
	</button>

	<a class="navbar-brand" href="index.php">
		<img src="images/home-icon.png" alt="Home" class="icon-small home-icon">
	</a>

	<button class="navbar-toggler right-toggler" type="button">
		<img src="images/user-icon.png" alt="Contact" class="icon-small user-icon">
	</button>
	
	<div class="left-panel panel">            
		<ul class="nav navbar-nav">
			<li class="nav-item">
				<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Page 1</a>
				<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
					<a class="dropdown-item" href="#">Page 1-1</a>
					<a class="dropdown-item" href="#">Page 1-2</a>
					<a class="dropdown-item" href="#">Page 1-3</a>
				</div>
			</li>
			<li class="nav-item"><a href="#" class="nav-link">Page 2</a></li>
		</ul>
	</div>

	<div class="right-panel panel">
		<ul class="nav navbar-nav">
			<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
				<li class="nav-item"><a href="admin.php" class="nav-link"><span class="glyphicon glyphicon-cog"></span> Admin</a></li>
			<?php endif; ?>
			<?php if (isset($_SESSION['username'])): ?>
				<li class="nav-item"><a href="profile.php" class="nav-link"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
				<li class="nav-item"><a href="logout.php" class="nav-link"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
			<?php else: ?>
				<li class="nav-item"><a href="register.php" class="nav-link"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
				<li class="nav-item"><a href="login.php" class="nav-link"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
			<?php endif; ?>
		</ul>
	</div>
</nav>

<script>

	document.addEventListener('DOMContentLoaded', function ()
	{
		const leftToggler = document.querySelector('.left-toggler');
		const rightToggler = document.querySelector('.right-toggler');
		const leftPanel = document.querySelector('.left-panel');
		const rightPanel = document.querySelector('.right-panel');

		leftToggler.addEventListener('click', function ()
		{
			leftPanel.classList.toggle('show-left');
			rightPanel.classList.remove('show-right');
		});

		rightToggler.addEventListener('click', function ()
		{
			rightPanel.classList.toggle('show-right');
			leftPanel.classList.remove('show-left');
		});
	});

</script>
