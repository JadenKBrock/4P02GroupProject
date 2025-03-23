<?php
session_start(); // Start the session if it's not already started
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";

function isActive($page) {
    return basename($_SERVER["PHP_SELF"]) == $page ? "active" : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : "Default"?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>styles/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php
        if (isset($page_styles)) {
            foreach ($page_styles as $style) {
                echo '<link rel="stylesheet" type="text/css" href="' . $base_url . 'styles/' . $style . '">' . "\n";
            }
        }
    ?>
    <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=67d6417b86189a0019fafc7a&product=sop' async='async'></script>
</head>
<body>

<div id="top-nav-bar">
    <div id="nav-title"><h2>News Portal</h2></div>
    <nav id="main-nav-bar">
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('index.php');?>">Dashboard</a>
        <a href="<?php echo $base_url;?>src/Generate/generate_page.php" class="<?php echo isActive('generate_page.php');?>">Generate</a>
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('about_us.php');?>">About Us</a>
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('faq.php');?>">FAQ</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- If user is logged in, show a profile circle with a dropdown for Logout -->
            <div class="profile-menu">
                <div class="profile-icon">
                    <?php 
                        // Display the first letter of the username if available, else a default letter
                        echo isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : "U"; 
                    ?>
                </div>
                <div class="dropdown-menu">
                    <a href="<?php echo $base_url; ?>logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo $base_url;?>src/Login/login_pageNew.php" class="<?php echo isActive('login_pageNew.php');?>">Login</a>
            <a href="<?php echo $base_url;?>src/Register/register_pageNew.php" class="<?php echo isActive('register_pageNew.php');?>">Sign Up</a>
        <?php endif; ?>
    </nav>
</div>
