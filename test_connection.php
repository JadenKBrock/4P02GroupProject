<?php
// Azure SQL Database connection details
$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

// Establish the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Check connection and output result
if ($conn) {
    echo "yes";
} else {
    echo "no";
}
?>
