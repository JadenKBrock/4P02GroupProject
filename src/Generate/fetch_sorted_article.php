<?php
include '../../includes/db_connection.php'; // Adjust if needed

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order = $_POST['order'] === 'oldest' ? 'ASC' : 'DESC';

    // Fetch the most recent or oldest article clearly based on user selection
    $query = "SELECT TOP 1 article_id, title, content, category FROM articles ORDER BY publishedAt $order";
    $result = sqlsrv_query($conn, $query);

    if ($result && $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        echo json_encode([
            'status' => 'success',
            'title' => $row['title'],
            'content' => $row['content'],
            'category' => $row['category']
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No articles found.']);
    }

    sqlsrv_close($conn);
}
?>
