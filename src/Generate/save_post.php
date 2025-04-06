<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Azure SQL Database connection details
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
        var_dump($_POST);  // add this
        exit;
    }

    // Establish the connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if (!$conn) {
        http_response_code(500);
        echo "Connection failed: " . print_r(sqlsrv_errors(), true);
        exit;
    }


    $sql = "INSERT INTO Posts (user_id, post_content, post_type) VALUES (?, ?, ?)";
    $params = array(3, $content, $type);

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "Statement prep failed: " . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    if (sqlsrv_execute($stmt)) {
        echo json_encode(["message" => "Post saved successfully."]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Post save error." . print_r(sqlsrv_errors(), true)]);
    }
}
?>