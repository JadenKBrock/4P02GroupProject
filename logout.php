<?php
    session_unset(); //null all session variables
    session_destroy(); //destroy session
    header("src/Login/login_pageNew.php"); 
    exit();
?>