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
    
    // Ensure session_start() is called at the beginning of the script that includes this navbar,
    // if you're using session-based functionality (like checking if a user is logged in)
    if (!session_id()) session_start();
    
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
    <div class="nav-inner">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="images/home-icon.png" alt="Home" class="icon-small home-icon">Your Website
            </a>
            
            <ul class="nav navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Page 1
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">Page 1-1</a>
                        <a class="dropdown-item" href="#">Page 1-2</a>
                        <a class="dropdown-item" href="#">Page 1-3</a>
                    </div>
                </li>
                <li class="nav-item"><a href="#" class="nav-link">Page 2</a></li>
            </ul>
            
            <div class="navbar-spacer"></div>
            
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a href="profile.php" class="nav-link"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="register.php" class="nav-link"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
                    <li class="nav-item"><a href="login.php" class="nav-link"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
