<?php
$page_title = "Generate";
$page_styles = ["main.css"]; // Ensure this matches your CSS file
include "../../views/header.php";
session_start();
include '../../includes/db_connection.php'; // Adjust if necessary

// Fetch random article with category initially
$query = "SELECT TOP 1 article_id, title, content, category FROM articles ORDER BY NEWID()";
$result = sqlsrv_query($conn, $query);
$article = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<h2>Generated Article</h2>

<!-- Sorting dropdown clearly added here -->
<label for="sortOrder">Sort Articles:</label>
<select id="sortOrder">
    <option value="newest">Newest First</option>
    <option value="oldest">Oldest First</option>
</select>

<button id="loadArticle">Load Article</button>

<hr>

<h3 id="articleTitle"><?php echo htmlspecialchars($article['title']); ?></h3>
<p id="articleContent"><?php echo nl2br(htmlspecialchars($article['content'])); ?></p>
<p><span class="article-category" id="articleCategory"><?php echo htmlspecialchars($article['category']); ?></span></p>

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
$(document).ready(function() {
    // AJAX handler for LLM requests
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
                    $("#responseText").text("Unexpected response format. Please try again.");
                }
            },
            error: function(xhr, status, err) {
                $("#responseText").text("An error occurred: " + err + ". Please try again.");
            }
        });
    });

    // AJAX handler for saving category
    $("#saveCategoryBtn").click(function() {
        var category = $("#articleCategory").text();

        $.ajax({
            url: "save_category.php",
            type: "POST",
            data: { category: category },
            success: function(response) {
                alert(response);
            },
            error: function(xhr, status, err) {
                alert("An error occurred: " + err + ". Please try again.");
            }
        });
    });

    // AJAX handler to load sorted articles
    $("#loadArticle").click(function() {
        let sortOrder = $("#sortOrder").val();

        $.ajax({
            url: "fetch_sorted_article.php",
            type: "POST",
            data: { order: sortOrder },
            dataType: "json",
            success: function(response) {
                if(response.status === "success"){
                    $("#articleTitle").text(response.title);
                    $("#articleContent").html(response.content.replace(/\n/g, '<br>'));
                    $("#articleCategory").text(response.category);
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, err) {
                alert('Error fetching article: ' + err);
            }
        });
    });
});
</script>

<?php
$page_scripts = [""];
include "../../views/footer.php";
?>
