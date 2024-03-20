<?php

    /*
    *
    * Author: Russell Elliott
    * Date Created: 2024/03/20
    *
    * This script is responsible for terminating the user's session
    * It unsets all session variables, destroys the session, and then redirects
    * the user back to the home page (index.php)
    * 
    */
    
    //Start the session to access existing session variables
    session_start();
    
    //Unset all session variables
    session_unset();
    
    //Destroy the session
    session_destroy();
    
    //Redirect the user to the home page
    header("Location: index.php");
    
    //Terminate the script to prevent further script execution
    exit;
    
?>
