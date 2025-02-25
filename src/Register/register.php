<?php
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit();
}

if (strlen($password) <= 6) {
    echo "<script>alert('Password must be greater than 6 characters.'); window.history.back();</script>";
    exit();
}

if (!preg_match('/[A-Z]/', $password)) {
    echo "<script>alert('Password must contain at least one uppercase letter.'); window.history.back();</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
}

// Check if username contains only allowed characters (alphanumeric and underscores)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    echo "<script>alert('Username contains invalid characters. Only alphanumeric characters and underscores are allowed.'); window.history.back();</script>";
    exit();
}

$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} else {
    // Check if search_table exists, if not, create it
    $check_table_sql = "SHOW TABLES LIKE 'search_table'";
    $result = $con->query($check_table_sql);
    if ($result->num_rows == 0) {
        // Table does not exist, create it
        $create_search_table_sql = "CREATE TABLE search_table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            table_name VARCHAR(255) NOT NULL
        )";
        if ($con->query($create_search_table_sql) !== TRUE) {
            die("Error creating search_table: " . $con->error);
        }
    }

    $stmt = $con->prepare("SELECT * FROM login WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt_result = $stmt->get_result();
    if ($stmt_result->num_rows > 0) {
        echo "Username or email already exists.";
    } else {
        $stmt = $con->prepare("INSERT INTO login (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $password);
        if ($stmt->execute()) {
            // create a new table for user history
            $user_id = $con->insert_id; // get the primary key of the inserted row
            $table_name = "user_{$user_id}_history";
            $create_table_sql = "CREATE TABLE $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                keyword VARCHAR(255),
                results JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            if ($con->query($create_table_sql) === TRUE) {
                // Insert the table name into search_table
                $insert_table_name_sql = "INSERT INTO search_table (table_name) VALUES (?)";
                $stmt_insert = $con->prepare($insert_table_name_sql);
                $stmt_insert->bind_param("s", $table_name);
                if ($stmt_insert->execute()) {
                    // Update the login table with the search table name
                    $update_login_sql = "UPDATE login SET search_table = ? WHERE User_ID = ?";
                    $stmt_update = $con->prepare($update_login_sql);
                    $stmt_update->bind_param("si", $table_name, $user_id);
                    if ($stmt_update->execute()) {
                        echo "Registration successful.";
                        header("Location: ../Login/index.html"); // redirect to login page
                        exit();
                    } else {
                        echo "Error updating login table: " . $stmt_update->error;
                    }
                } else {
                    echo "Error inserting table name into search_table: " . $stmt_insert->error;
                }
            } else {
                echo "Error creating user history table: " . $con->error;
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

