<?php
//$base_url = "http://localhost:8080/";
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Gonna need to add SQL stuff to this later
    // For example:
    // $email = $_POST['email'];
    // $password = $_POST['password'];

    // After processing, redirect to index.php
    header("Location: index.php");
    exit();
}
$page_title = "Login";
$page_styles = ["login-register.css"];
include "../../views/header.php";
?>

<div class="main-container">
    <div class="content-container">
        <h2>Login</h2>
        <div class="form-container">
            <form action="register.php" method="post" class="main-form">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="login-signup-redirect">
                <p>Don't have an account?</p>
                <a href="<?php echo $base_url;?>src/Register/register_page.php" class="login-signup-link">Register Here</a>
            </div>
        </div>
    </div>
</div>


<?php
$page_scripts = ["login_script.js"];
include "../../views/footer.php";
?>