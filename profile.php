<?php

    /*
    * 
    * Author: Russell Elliott
    * Date Created: 2024/03/20
    * 
    * This script serves as the template for the user's profile page
    * It begins a new session or continues the current one and includes a responsive navigation bar and footer
    * This page is intended to display user-specific information (to be implemented)
    * Customize the meta tags and content to suit your website's SEO and content strategy
    * 
    */
    
    session_start();
    
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

        <h1>Your Profile</h1>

    <?php include('footer.php'); ?>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
