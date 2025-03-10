<?php
//$base_url = "http://localhost:8080/";
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
</head>
<body>

<div id="top-nav-bar">
    <div id="nav-title"><h2>News Portal</h2></div>
    <nav id="main-nav-bar">
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('index.php');?>">Dashboard</a>
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('about_us.php');?>">About Us</a>
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('faq.php');?>">FAQ</a>
        <a href="<?php echo $base_url;?>src/Login/login_page.php" class="<?php echo isActive('login_page.php');?>">Login</a>
        <a href="<?php echo $base_url;?>src/Register/register_page.php" class="<?php echo isActive('register_page.php');?>">Sign Up</a>
    </nav>
</div>