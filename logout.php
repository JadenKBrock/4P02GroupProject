<?php
    session_start();
    session_unset(); //null all session variables
    session_destroy(); //destroy session
    header("Location: " . $base_url . "index.php");
    exit();    
?>