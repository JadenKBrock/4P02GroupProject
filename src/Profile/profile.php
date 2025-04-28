<?php
session_start();
$user_id = $_SESSION['user_id']; 

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

    $user_sql = "SELECT first_name, last_name, username, email FROM users WHERE id = ?";
    $user_stmt = sqlsrv_prepare($conn, $user_sql, array(&$user_id));
    sqlsrv_execute($user_stmt);
    $user_info = sqlsrv_fetch_array($user_stmt, SQLSRV_FETCH_ASSOC);

    $con->close();
}
?>