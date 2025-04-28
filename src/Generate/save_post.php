<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
  ]);
  session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Azure SQL Database connection info
    $serverName = "ts19cpsqldb.database.windows.net";
    $connectionOptions = array(
        "Database" => "ts19cpdb3p96",
        "Uid" => "ts19cp",
        "PWD" => "@Group93p96",
        "TrustServerCertificate" => true
    );

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!$data) {
        http_response_code(400);
        echo "Invalid JSON or no data received.";
        error_log("RAW INPUT: " . $raw);
        exit;
    }

    $content = $data["post_content"] ?? "";
    $type = $data["post_type"] ?? "";

    if (!$content || !$type) {
        http_response_code(400);
        echo "Missing content." . $data;
        var_dump($_POST);
        exit;
    }

    // Establish the sqlsrv connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if (!$conn) {
        http_response_code(500);
        echo "Connection failed: " . print_r(sqlsrv_errors(), true);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO Posts (user_id, post_content, post_type) VALUES (?, ?, ?)";
    $params = array($user_id, $content, $type);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "Statement prep failed: " . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    // Execute query
    if (sqlsrv_execute($stmt)) {
        echo json_encode(["message" => "Post saved successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Post save error." . print_r(sqlsrv_errors(), true)]);
    }
}
?>