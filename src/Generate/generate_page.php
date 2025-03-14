<?php
$page_title = "Generate";
$page_styles = [""];
include "../../views/header.php";
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<h2>PHP to Python Example</h2>
    <input type="text" id="userInput" placeholder="Enter something">
    <button id="sendRequest">Send to Python</button>

    <p>Response: <span id="responseText"></span></p>

    <script>
        $(document).ready(function() {
            $("#sendRequest").click(function() {
                var userInput = $("#userInput").val();  // Get input value

                $.ajax({
                    url: "run_generate_py.php",  // Calls PHP script
                    type: "POST",
                    data: { input: userInput },  // Send data as form data
                    success: function(response) {
                        console.log("Server Response: ", response);
                        $("#responseText").text(response.message);  // Update page
                    },
                    error: function(xhr, status, err) {
                        console.error("AJAX Error: ", status, err);
                        console.log("Server Response: ", xhr.responseText);
                        $("#responseText").text("Error: Could not execute script.");
                    }
                });
            });
        });
    </script>

<?php
$page_scripts = [""];
include "../../views/footer.php";
?>



