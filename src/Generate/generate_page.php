<?php
$page_title = "Generate";
$page_styles = [""];
include "../../views/header.php";
session_start();
include '../../includes/db_connection.php'; // Adjust this path if necessary

// Fetch random article clearly with category
$query = "SELECT article_id, title, content, category FROM articles ORDER BY RAND() LIMIT 1";
$result = mysqli_query($conn, $query);
$article = mysqli_fetch_assoc($result);
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<h2>Generated Article</h2>

<h3><?php echo htmlspecialchars($article['title']); ?></h3>
<p><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
<p><strong>Category:</strong> <span id="articleCategory"><?php echo htmlspecialchars($article['category']); ?></span></p>

<button id="saveCategoryBtn">Save Category</button>

<hr>

<h2>Testing LLM</h2>
<label for="contentType">Content Type:</label>
<input type="text" id="contentType">
<br><br>
<label for="contentText">Content Text:</label>
<input type="text" id="contentText">
<br><br>
<button id="sendRequest">Get Response</button>

<p>Response: <span id="responseText"></span></p>

<script>
// AJAX to handle LLM request
$(document).ready(function() {
    $("#sendRequest").click(function() {
        var contentType = $("#contentType").val();
        var contentText = $("#contentText").val();

        $.ajax({
            url: "run_generate_py.php",
            type: "POST",
            data: JSON.stringify({ 
                content_type: contentType, 
                content_text: contentText 
            }),
            contentType: "application/json",
            dataType: "json",
            success: function(response) {
                if (response && response.response) {
                    $("#responseText").text(response.response);
                } else {
                    $("#responseText").text("Error: Unexpected response format");
                }
            },
            error: function(xhr, status, err) {
                $("#responseText").text("Error: Could not execute request.");
            }
        });
    });

    // AJAX to handle category saving
    $("#saveCategoryBtn").click(function() {
        var category = $("#articleCategory").text();

        $.ajax({
            url: "save_category.php",
            type: "POST",
            data: { category: category },
            success: function(response) {
                alert(response);
            },
            error: function() {
                alert('Error saving category.');
            }
        });
    });
});
</script>

<?php
$page_scripts = [""];
include "../../views/footer.php";
?>
