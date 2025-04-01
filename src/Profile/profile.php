<?php

session_start();
$user_id = $_SESSION['id'];  
//$email = $_POST['email'];
//$password = $_POST['password'];

$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");


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
} else {
    $sql = "SELECT frequency, day_of_week, day_of_month, generation_time FROM content_generation_frequency WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $con->close();
}
?>