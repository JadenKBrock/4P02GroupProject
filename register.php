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

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form fields and perform basic validation
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($username) || empty($email) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the Users table using a parameterized query
        $tsql = "INSERT INTO Users (username, email, password) VALUES (?, ?, ?)";
        $params = array($username, $email, $hashedPassword);
        $stmt = sqlsrv_query($conn, $tsql, $params);

        if ($stmt === false) {
            echo "Registration error: " . print_r(sqlsrv_errors(), true);
        } else {
            echo "Registration successful! You can now <a href='login.php'>login</a>.";
        }
        sqlsrv_free_stmt($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h1>Register New User</h1>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required /><br /><br />
        <label>Email:</label>
        <input type="email" name="email" required /><br /><br />
        <label>Password:</label>
        <input type="password" name="password" required /><br /><br />
        <button type="submit">Register</button>
    </form>
</body>
</html>
