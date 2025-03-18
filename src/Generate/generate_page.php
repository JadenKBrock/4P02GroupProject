<?php
$page_title = "Generate";
$page_styles = [""];
include "../../views/header.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    $("#sendRequest").click(function() {
        var contentType = $("#contentType").val();  // Get content type
        var contentText = $("#contentText").val();  // Get content text

        $.ajax({
            url: "run_generate_py.php",  // Calls updated PHP script
            type: "POST",
            data: JSON.stringify({ 
                content_type: contentType, 
                content_text: contentText 
            }),
            contentType: "application/json",
            dataType: "json",  // Ensure response is treated as JSON
            success: function(response) {
                console.log("Server Response: ", response);
                
                // Ensure we correctly access the response
                if (response && response.response) {
                    $("#responseText").text(response.response);  // Show response from Azure
                } else {
                    $("#responseText").text("Error: Unexpected response format");
                }
            },
            error: function(xhr, status, err) {
                console.error("AJAX Error: ", status, err);
                console.log("Server Response: ", xhr.responseText);
                $("#responseText").text("Error: Could not execute request.");
            }
        });
    });
});
</script>

<?php
$page_scripts = [""];
include "../../views/footer.php";
?>
