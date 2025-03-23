<?php
session_start();
include '../../includes/db_connection.php'; // Adjust this path if necessary

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
    $content_type = trim($data['content_type'] ?? 'text');
    $content_text = trim($data['content_text'] ?? '');

    // Validate input
    if (empty($content_text)) {
        echo json_encode(["error" => "Missing 'content_text' in request"]);
        exit;
    }

    // Azure Function URL
    $azure_function_url = "https://llmfunctionapp2.azurewebsites.net/api/llm-call?code=PCoE-HGnTYpC7XgL9p0I9glgIeUY8MpozIn2Ljeq0LsOAzFuZA7zkA==";

    // Prepare JSON payload
    $json_data = json_encode([
        "content_type" => $content_type,
        "content_text" => $content_text
    ]);

    // Initialize cURL
    $ch = curl_init($azure_function_url);
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
        curl_close($ch);
        exit;
    } elseif ($http_code !== 200) {
        echo json_encode(["error" => "Azure Function returned HTTP $http_code", "details" => $response]);
        curl_close($ch);
        exit;
    }

    // Decode response clearly to handle data
    $response_data = json_decode($response, true);

    // Assume response has keys: title, content, category (adjust as per your actual Azure response)
    $article_title = $content_text; // Use content_text as title if title isn't returned
    $article_content = $response_data['response'] ?? '';
    $article_category = $content_type; // Using content_type as category clearly

    // Insert the article into SQL Server database
    $sql = "INSERT INTO articles (source, author, title, description, url, urlToImage, publishedAt, content, category)
            VALUES (?, ?, ?, ?, ?, ?, GETDATE(), ?, ?)";

    // Prepare parameters (NULL placeholders clearly used if no actual data)
    $params = [
        'Azure Function',            // source
        'Automated Aggregator',      // author
        $article_title,              // title
        NULL,                        // description
        NULL,                        // url
        NULL,                        // urlToImage
        $article_content,            // content
        $article_category            // category
    ];

    $stmt = sqlsrv_prepare($conn, $sql, $params);

    // Execute SQL statement
    if (!sqlsrv_execute($stmt)) {
        echo json_encode(["error" => "Database insertion failed", "details" => sqlsrv_errors()]);
        sqlsrv_close($conn);
        exit;
    }

    // Return Azure Function response
    echo json_encode(["response" => $article_content, "status" => "Article saved successfully"]);

    // Close connections
    sqlsrv_close($conn);
    curl_close($ch);
}
?>
