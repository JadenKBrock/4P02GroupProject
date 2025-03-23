<?php
    session_start();
    session_unset(); //null all session variables
    session_destroy(); //destroy session
    header("src/Login/login_page.php"); //redirect to login page
    exit();
?>
