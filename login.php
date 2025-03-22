<?php
// Connection parameters – update these with your actual details
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

// Establish the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Retrieve the user's record from the Users table
    $tsql = "SELECT * FROM Users WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_query($conn, $tsql, $params);

    if ($stmt === false) {
        echo "Login error: " . print_r(sqlsrv_errors(), true);
    } else {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            echo "Login successful! Welcome, " . htmlspecialchars($username) . ".";
            // Here you might start a session and redirect the user
        } else {
            echo "Invalid username or password.";
        }
    }
    sqlsrv_free_stmt($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required /><br /><br />
        <label>Password:</label>
        <input type="password" name="password" required /><br /><br />
        <button type="submit">Login</button>
    </form>
</body>
</html>
