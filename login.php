<?php
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bc-background-blockchain-1">
    <header class="header">
        <div class="logo-container">
            <img src="../assets/logo.png" alt="Company Logo" class="logo">
        </div>
    </header>
    <div class="login-container bc-background-blockchain-2">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <!-- Input field for email -->
            <input type="email" id="email" name="email" placeholder="Email" required>
            <!-- Input field for password -->
            <input type="password" id="password" name="password" placeholder="Password" required>
            <!-- Submit button for the form -->
            <button type="submit" class="bc-background-blockchain-4">Login</button>
        </form>
        <!-- Link to the registration page -->
        <a href="../Register/index.html" class="register-link">Don't have an account? Register here</a>
    </div>
    <div class="others">
        <div id="patch-note" class="patch-note"></div>
        <script src="patchNote.js"></script>
    </div>
</body>
</html>
