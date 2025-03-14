<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["message" => "Invalid request."]);
    exit;
}

$frequency = $data["frequency"];
$customDate = $data["customDate"] ?? null;
$customTime = $data["customTime"] ?? null;

$conn = new mysqli("localhost", "root", "", "news_portal");
if ($conn->connect_error) {
    echo json_encode(["message" => "Database connection failed."]);
    exit;
}

$query = "INSERT INTO newsletter_schedule (frequency, custom_date, custom_time) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $frequency, $customDate, $customTime);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["message" => "Schedule set successfully."]);
} else {
    echo json_encode(["message" => "Failed to set schedule."]);
}

$stmt->close();
$conn->close();
?>