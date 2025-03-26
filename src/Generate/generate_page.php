<?php
$page_title = "Generate";
$page_styles = ["generate.css"];
include "../../views/header.php";
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="generate-container">
    <div class="input-outer-container">
        <h3>Generate Post</h3>
        <div class="format-container">
            <div class="dropdown-container">
                <button onclick="dropdown()" class="formatbtn">Format</button>
                <div id="dropdown" class="dropdown-content">
                    <button onclick="selectFormat('Facebook')">Facebook</button>
                    <button onclick="selectFormat('Twitter')">Twitter</button>
                    <button onclick="selectFormat('Email')">Email</button>
                </div>
            </div>
            <p id="format-selection">Facebook</p>
        </div>
        <div class="input-container">
            <input type="text" id="userInput" placeholder="Enter a topic or keyword.">
            <button id="sendRequest">Generate</button>
        </div>
    </div>
    <div class="output_container">
        
    </div>
</div>

<p>Response: <span id="responseText"></span></p>

<script>

function selectFormat(format) {
    $("#format-selection").text(format);
}

$(document).ready(function() {
    $("#sendRequest").click(function() {
        var format = $("#format-selection").val();  // Get content type
        var userInput = $("#userInput").val();  // Get content text

        $.ajax({
            url: "run_generate_py.php",  // Calls updated PHP script
            type: "POST",
            data: JSON.stringify({ 
                format_type: format, 
                keyword: userInput 
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
