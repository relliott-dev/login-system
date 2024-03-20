<?php

    /*
    * Author: Russell Elliott
    * Date Created: 2024/01/04
    *
    * Retrieves the real IP address of the client. Prioritizes HTTP_CLIENT_IP,
    * then HTTP_X_FORWARDED_FOR, and finally REMOTE_ADDR
    * 
    */

    //Initialize variable to store the real IP address
    $real_ip_address = "";

    //Check for IP address in HTTP_CLIENT_IP
    if (isset($_SERVER['HTTP_CLIENT_IP']))
    {
        $real_ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }
    //Check for the first IP in HTTP_X_FORWARDED_FOR
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $real_ip_address = trim($ips[0]); // Trim to remove any extra whitespace
    }
    //Default to REMOTE_ADDR if other headers are not set
    elseif (isset($_SERVER['REMOTE_ADDR']))
    {
        $real_ip_address = $_SERVER['REMOTE_ADDR'];
    }

?>
