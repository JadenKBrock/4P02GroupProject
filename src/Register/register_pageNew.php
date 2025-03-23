<?php
// Process registration logic
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // Insert user data into the Users table
        $tsql   = "INSERT INTO Users (username, email, password) VALUES (?, ?, ?)";
        $params = array($username, $email, $hashedPassword);
        $stmt   = sqlsrv_query($conn, $tsql, $params);

        if ($stmt === false) {
            $message = "Registration error: " . print_r(sqlsrv_errors(), true);
        } else {
            $message = "Registration successful! You can now <a href='login_pageNew.php'>login</a>.";
        }
        sqlsrv_free_stmt($stmt);
    }
}

// Set page variables for header/footer
$page_title  = "Register";
$page_styles = ["login-register.css"];
include "./views/header.php";
?>

<div class="main-container">
    <div class="content-container">
        <h2>Register</h2>
        <?php if(!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="form-container">
            <form action="register_page.php" method="post" class="main-form">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <div class="login-signup-redirect">
                <p>Already have an account?</p>
                <a href="<?php echo $base_url; ?>src/Login/login_pageNew.php" class="login-signup-link">Login Here</a>
            </div>
        </div>
    </div>
</div>

<?php
$page_scripts = ["register_script.js"];
include "./views/footer.php";
?>
