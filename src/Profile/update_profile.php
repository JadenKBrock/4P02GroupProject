<?php

$base_url = "http://localhost:8080/";
//$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/"; 

$page_title = "Update Profile";
//$page_styles = ["update_profile.css"];
include "../../views/header.php";

$user_id = $_SESSION["user_id"];
$frequency = $_POST['frequency'];
$day_of_week = $_POST['day_of_week'] ?? null;
$day_of_month = $_POST['day_of_month'] ?? null;
$generation_time = $_POST['generation_time'] ?? '00:00:00';

if ($frequency === 'daily') {
    $generation_time = '00:00:00';
    $day_of_week = null;
    $day_of_month = null;
} elseif ($frequency === 'weekly') {
    $day_of_month = null;
} elseif ($frequency === 'monthly') {
    $day_of_week = null;
}

$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$check_sql = "SELECT id FROM content_generation_frequency WHERE user_id = ?";
$stmt = $con->prepare($check_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if ($exists) {
    $sql = "UPDATE content_generation_frequency SET frequency = ?, day_of_week = ?, day_of_month = ?, generation_time = ? WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssi", $frequency, $day_of_week, $day_of_month, $generation_time, $user_id);
} else {
    $sql = "INSERT INTO content_generation_frequency (user_id, frequency, day_of_week, day_of_month, generation_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("issss", $user_id, $frequency, $day_of_week, $day_of_month, $generation_time);
}

if ($stmt->execute()) {
    header("Location: /profile_page.php");
    exit();
} else {
    echo "Error updating preferences.";
}

$stmt->close();
$con->close();
?>