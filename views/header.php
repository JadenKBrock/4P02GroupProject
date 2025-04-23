<?php
//session_start();

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
    <meta property="og:title" content="SmartSummaries: AI-Powered Newsletter & Social Media Content Generator" />
    <meta property="og:description" content="Create engaging newsletters and social media posts effortlessly with AI-driven automation, summarization, and scheduling." />
    <meta property="og:url" content="<?= isset($page_url) ? $page_url : $base_url . basename($_SERVER['PHP_SELF']); ?>" />
    <meta name="twitter:title" content="AI-Powered Newsletter & Social Media Content Generator" />
    <meta name="twitter:description" content="Create engaging newsletters and social media posts effortlessly with AI-driven automation, summarization, and scheduling." />
    
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url;?>styles/main.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php
        if (isset($page_styles)) {
            foreach ($page_styles as $style) {
                echo '<link rel="stylesheet" type="text/css" href="' . $base_url . 'styles/' . $style . '">' . "\n";
            }
        }
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url; ?>styles/responsive.css">

    
            <script async src="https://static.addtoany.com/menu/page.js"></script>
</head>
<body>

<div id="top-nav-bar">
    <div id="nav-title"><h2>News Portal</h2></div>
    <nav id="main-nav-bar">
        <a href="<?php echo $base_url;?>index.php" class="<?php echo isActive('index.php');?>">Dashboard</a>
        <a href="<?php echo $base_url;?>src/Generate/generate_page.php" class="<?php echo isActive('generate_page.php');?>">Generate</a>
        <a href="<?php echo $base_url;?>src/AboutUs/about_us.php" class="<?php echo isActive('about_us.php');?>">About Us</a>
        <a href="<?php echo $base_url;?>src/FAQ/faq.php" class="<?php echo isActive('faq.php');?>">FAQ</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo $base_url;?>src/Profile/profile_page.php" class="<?php echo isActive('profile_page.php');?>">Profile</a>
            <a href="<?php echo $base_url;?>logout.php" class="nav-btn" style="margin-left: 10px; color: red;">Logout</a>
        <?php else: ?>
            <a href="<?php echo $base_url;?>src/Login/login_pageNew.php" class="<?php echo isActive('login_pageNew.php');?>">Login</a>
            <a href="<?php echo $base_url;?>src/Register/register_pageNew.php" class="<?php echo isActive('register_pageNew.php');?>">Register</a>
        <?php endif; ?>
    </nav>
</div>
