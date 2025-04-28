<?php
//sets secure session cookie parameters before starting the session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
//$base_url = "http://localhost:8080/";
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";

//Connects to Azure SQL db
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

//check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Trim username input
    $username = trim($_POST['username']);
    $password = $_POST['password']; //raw password

    //sql query to fetch user by username
    $tsql   = "SELECT * FROM Users WHERE username = ?";
    $params = array($username); //binds parameter which prevents SQL injection
    $stmt   = sqlsrv_query($conn, $tsql, $params); //execute query

    if ($stmt === false) {
        $message = "Login error: " . print_r(sqlsrv_errors(), true);
    } else {

        //fetch user record as associative array
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        //verify password against hashed password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for logged in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            sqlsrv_free_stmt($stmt);

            //redirect to dashboard page
            header("Location: " . $base_url . "index.php");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
    sqlsrv_free_stmt($stmt);
}

//page title and styling
$page_title  = "Login";
$page_styles = ["login-register.css"];
include "../../views/header.php";
?>
<div class="main-container">
    <div class="content-container">
        <h2>Login</h2>
        <?php if(!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="form-container">
            <form action="login_pageNew.php" method="post" class="main-form">
                <input type="text" id="username" name="username" placeholder="Username" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="login-signup-redirect">
                <p>Don't have an account?</p>
                <a href="<?php echo $base_url; ?>src/Register/register_pageNew.php" class="login-signup-link">Register Here</a>
            </div>
        </div>
    </div>
</div>
<?php
$page_scripts = ["login_script.js"];
include "../../views/footer.php";
//ob_end_flush()
?>
