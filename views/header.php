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
        <div id="user-nav">
            <?php if (isset($_SESSION['user_id'])): ?>  
                <div class="profile-dropdown">
                    <img src="<?php echo $_SESSION['profile_pic'] ?? 'default-profile.png'; ?>" 
                        alt="Profile" class="profile-icon" id="profile-icon">
                    <div class="dropdown-menu hidden" id="profile-dropdown">
                        <a href="profile.php">My Profile</a>
                        <a href="logout.php">Log Out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="nav-btn">Login</a>
                <a href="register.php" class="nav-btn">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</div>