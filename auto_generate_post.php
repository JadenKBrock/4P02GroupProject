<?php
$now = new DateTime("now", new DateTimeZone("EST"));
$dayOfWeek = $now->format('l');
$dayOfMonth = $now->format('j');
$timeNow = $now->format('H:i:s');

// Connect to database
$con = new mysqli("localhost", "root", "Tsj123456+", "4p02_group_login_db");
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = [
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Get users whose content generation frequency matches current time
$sql = "SELECT user_id, frequency FROM content_generation_frequency 
        WHERE (frequency = 'daily' 
        OR (frequency = 'weekly' AND day_of_week = ?)
        OR (frequency = 'monthly' AND day_of_month = ?))";

$stmt = $con->prepare($sql);
$stmt->bind_param("si", $dayOfWeek, $dayOfMonth);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $frequency = $row['frequency'];

    // Get the last topic used to generate a post by the user
    $topic_sql = "SELECT TOP 1 post_content FROM Posts WHERE user_id = ? ORDER BY creation_date DESC";
    $params = [$user_id];
    $topic_stmt = sqlsrv_query($conn, $topic_sql, $params);
    $topic_row = sqlsrv_fetch_array($topic_stmt, SQLSRV_FETCH_ASSOC);
    $last_topic = $topic_row ? $topic_row['post_content'] : "default topic";

    // Azure Function URL
    $ch = curl_init("https://llmfunctionapp2.azurewebsites.net/api/get_urls?code=tNj_7CzAU4N3LvACejo__-gfQTw9d_wsKDVUQUF6O8D2AzFuOJP2MQ==");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["keyword" => $last_topic]));
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    curl_close($ch);

    if (!isset($data['result'])) continue;
    $generated_post = $data['result'];

    // Save post to be displayed on the dashboard
    $insert_sql = "INSERT INTO Posts (user_id, post_content, post_type) VALUES (?, ?, ?)";
    $insert_params = [$user_id, $generated_post];
    $insert_stmt = sqlsrv_query($conn, $insert_sql, $insert_params);

    // Get user's email to send an email notification that a new post has been generated
    $user_sql = "SELECT email FROM users WHERE id = ?";
    $email_stmt = sqlsrv_query($conn, $user_sql, [$user_id]);
    $user_info = sqlsrv_fetch_array($email_stmt, SQLSRV_FETCH_ASSOC);
    $email = $user_info['email'];

    // Send the email notification
    $subject = "New Content Generated for You";
    $message = "Hi! A new post has been generated and added to your dashboard:\n\n" . $generated_post;
    $headers = "From: bigsexy@example.com";

    mail($email, $subject, $message, $headers);
}

$con->close();
sqlsrv_close($conn);
?>