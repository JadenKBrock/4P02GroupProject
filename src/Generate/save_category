<?php
session_start();
include '../../includes/db_connection.php'; // Adjust path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    // Check if user is logged in clearly
    if (!isset($_SESSION['user_id'])) {
        echo "Please log in to save categories.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO saved_categories (user_id, category) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $category);

    if ($stmt->execute()) {
        echo "Category saved successfully!";
    } else {
        echo "Error saving category.";
    }

    $stmt->close();
}
?>
