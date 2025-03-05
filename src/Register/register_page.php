<?php
//$base_url = "http://localhost:8080/";
$base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/";

$page_title = "Register";
$page_styles = ["login-register.css"];
include "../../views/header.php";
?>

<div class="main-container">
    <div class="content-container">
        <h2>Register</h2>
        <div class="form-container">
            <form action="register.php" method="post" class="main-form">
                <div class="name-fields">
                    <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                </div>
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
            </form>
            <div class="login-signup-redirect">
                <p>Already have an account?</p>
                <a href="<?php echo $base_url;?>src/Login/login_page.php" class="login-signup-link">Login Here</a>
            </div>
        </div>
    </div>
</div>

<?php
$page_scripts = ["register_script.js"];
include "../../views/footer.php";
?>