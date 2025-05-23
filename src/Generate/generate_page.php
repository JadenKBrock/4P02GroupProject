<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
$page_title = "Generate";
$page_styles = ["generate.css"];
include "../../views/header.php";
session_start();
include '../../includes/db_connection.php'; // Adjust if necessary

// Fetch random article with category initially
$query = "SELECT TOP 1 article_id, title, content, category FROM articles ORDER BY NEWID()";
$result = sqlsrv_query($conn, $query);
$article = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="generate-container">
    <div class="input-outer-container">
        <h3>Generate Post!</h3>
        <div class="format-container">
            <div class="dropdown-container">
                <button onclick="dropdown()" class="formatbtn">Format</button>
                <div id="dropdown" class="dropdown-content">
                    <button onclick="selectFormat('Facebook')" class="dropdown-btn">Facebook</button>
                    <button onclick="selectFormat('Twitter')" class="dropdown-btn">Twitter</button>
                    <button onclick="selectFormat('Email')" class="dropdown-btn">Email</button>
                </div>
            </div>
            <p id="format-selection">Facebook</p>
        </div>
        <div class="input-container">
            <input type="text" id="userInput" placeholder="Enter a topic or keyword.">
            <button id="sendRequest">Generate</button>
        </div>
    </div>
    <div class="loading-container" id="loadingContainer" style="display: none;">
        <div class="loading-spinner"></div>
        <p>Generating post, please wait...</p>
    </div>
    <div class="output_container" id="responseBox">
        <div class="output_main_text">Choose a post to save</div>
    </div>
</div>

<!--<div class="post_card_container">
            <p class="post_content">This is a post about some random thing This is a post about some random thing This is a post about some random thing This is a post about some random thing This is a post about some random thing This is a post about some random thing This is a post about some random thing</p>
            <button class="save_btn">Save</button>
</div>-->

<style>
.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>

function selectFormat(format) {
    $("#format-selection").text(format);
}

// Get a response from the LLM using run_generate_py.php and fill the page with the returned posts
$(document).ready(function() {
    // AJAX handler for LLM requests
    $("#sendRequest").click(function() {
        var format = $("#format-selection").text(); 
        var userInput = $("#userInput").val();
        
        if (!userInput) {
            alert("Please enter a topic or keyword!");
            return;
        }

        // show loading animation
        $("#loadingContainer").show();
        $("#responseBox").empty();
        
        console.log(format);
        console.log(userInput);
        let url_index = 0;

        // callGenerate is called 5 times, 1 time per url generated
        function callGenerate(urlIndex) {
            if (urlIndex > 4) {
                // all requests completed, hide loading animation
                $("#loadingContainer").hide();
                return;
            }
            $.ajax({
                url: "run_generate_py.php",
                type: "POST",
                data: JSON.stringify({ 
                    format_type: format, 
                    keyword: userInput,
                    url_index: urlIndex
                }),
                contentType: "application/json",
                dataType: "json",
                success: function(response) {
                    console.log(`Server Response for url ${urlIndex}: `, response);
                    
                    if (response && response.response) {
                        $(".output_container").css("display", "flex");
                        let postResponse = `
                        <div class="post_card_container">
                            <p class="post_content">${response.response}</p>
                            <button class="save_btn">Save</button>
                        </div>
                        `;

                        $("#responseBox").append(postResponse);
                    } else {
                        $("#responseText").text("Error: Unexpected response format");
                    }
                    callGenerate(urlIndex + 1)
                },
                error: function(xhr, status, err) {
                    console.error("AJAX Error: ", status, err);
                    console.log("Server Response: ", xhr.responseText);
                    $("#responseText").text("Error: Could not execute request.");
                    $("#loadingContainer").hide();
                }
            });
        }
        callGenerate(url_index);
    });
});

$(document).on("click", ".save_btn", function () {
    var postContent = $(this).siblings(".post_content").text();
    var postType = $("#format-selection").text();

    console.log(postContent);
    console.log(postType);

    // Save the post to the DB using save_post.php
    $.ajax({
            url: "save_post.php",  
            type: "POST",
            data: JSON.stringify({ 
                post_content: postContent, 
                post_type: postType 
            }),
            contentType: "application/json",
            dataType: "json", 
            success: function(response) {
                alert("Post saved!");
                console.log("Saved response:", response);
            },
            error: function(xhr, status, err) {
                console.error("Save error:", status, err);
                console.log("Response text:", xhr.responseText);
                alert("Failed to save post.");
            }
        });

});

</script>

<?php
$page_scripts = [""];
include "../../views/footer.php";
?>
