<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input from AJAX request
    
    $input = escapeshellarg($_POST['input']);

    // Execute the Python script and capture the output
    $command = "python ../../Generator/summarize.py $input";
    $output = shell_exec($command);

    // Return the Python script output as JSON
    header('Content-Type: application/json');
    echo json_encode(["message" => $output]);
    #echo json_encode(["message" => "yes"]);
}
?>