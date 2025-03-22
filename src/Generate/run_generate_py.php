<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Read the raw JSON input
    $json_input = file_get_contents("php://input");
    $data = json_decode($json_input, true);

    // Ensure JSON decoding is successful
    if (!$data) {
        echo json_encode(["error" => "Invalid JSON input"]);
        exit;
    }

    // Extract data from request
    $keyword = trim($data['keyword'] ?? '');

    // Validate input
    if (empty($keyword)) {
        echo json_encode(["error" => "Missing 'keyword' in request"]);
        exit;
    }

    // Azure Function URL
    $azure_function_url = "https://llmfunctionapp2.azurewebsites.net/api/get_urls?code=tNj_7CzAU4N3LvACejo__-gfQTw9d_wsKDVUQUF6O8D2AzFuOJP2MQ==";

    // Prepare JSON payload
    $json_data = json_encode([
        "keyword" => $keyword,
    ]);

    // Initialize cURL
    $ch = curl_init($azure_function_url);

    // Set cURL options for a POST request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    // Execute cURL request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if ($response === false) {
        echo json_encode(["error" => "cURL Error: " . curl_error($ch)]);
    } elseif ($http_code !== 200) {
        echo json_encode(["error" => "Azure Function returned HTTP $http_code", "details" => $response]);
    } else {
        echo $response; // Return Azure Function response
    }

    // Close cURL
    curl_close($ch);
}
?>
