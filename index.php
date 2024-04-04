<?php

	/*
	* 
	* Author: Russell Elliott
	* Date Created: 2024/03/20
	* 
	* This template serves as the home page for the website
	* It includes a responsive navigation bar and footer, dynamically included via PHP
	* Customize the meta tags and content to suit your website's SEO and content strategy
	* 
	*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
	<meta charset="UTF-8">
	<title>Home Page</title>
	<meta name="description" content="A short description of the page's content.">
	<meta name="keywords" content="keyword1, keyword2, keyword3">
	<meta name="author" content="Author's Name">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="style.css">
    
</head>
<body>
    
	<?php include('navbar.php'); ?>

	<section class="hero-section text-center d-flex justify-content-center align-items-center">
	<div>
		<h1>Welcome to Our Professional Website</h1>
		<p>Delivering excellence in every service we offer</p>
        	<a href="#services" class="btn btn-primary">Explore Our Services</a>
	</div>
	</section>

	<section class="about-section container">
		<h2>About Us</h2>
		<p>We are a team of dedicated professionals committed to delivering high-quality services to our clients. Our focus is on customer satisfaction and continuous improvement.</p>
	</section>

	<section class="services-section container">
	<h2>Our Services</h2>
	<div class="row">
		<div class="col">
			<h3>Service One</h3>
			<p>Our first service offers unparalleled quality and reliability.</p>
		</div>
		<div class="col">
			<h3>Service Two</h3>
			<p>Discover the benefits of our second service, tailored to meet your needs.</p>
		</div>
		<div class="col">
			<h3>Service Three</h3>
			<p>Our third service provides innovative solutions for modern challenges.</p>
		</div>
	</div>
	</section>

	<?php include('footer.php'); ?>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
