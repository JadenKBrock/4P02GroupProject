<?php
session_start();

//$base_url = "http://localhost:8080/";
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/"; 

$page_title = "Update Profile";
//$page_styles = ["update_profile.css"];
include "../../views/header.php";

$user_id = $_SESSION["user_id"];
$newsletter_frequency = $_POST['newsletter_frequency'];
$custom_date = $_POST['custom_date'] ?? null;
$custom_time = $_POST['custom_time'] ?? null;

$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Prepare SQL with custom date and time handling
$sql = "UPDATE users 
        SET newsletter_frequency = ?, custom_date = ?, custom_time = ? 
        WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("sssi", $newsletter_frequency, $custom_date, $custom_time, $user_id);

if ($stmt->execute()) {
    header("Location: http://127.0.0.1:5000");
    exit();
} else {
    echo "Error updating profile.";
}

sqlsrv_free_stmt($stmt);

$stmt->close();
$con->close();
?>