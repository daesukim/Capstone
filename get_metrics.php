<?php
// get_metrics.php

// Your database credentials
$servername = "db.luddy.indiana.edu";
$username = "i494f23_team25";
$password = "my+sql=i494f23_team25";
$database = "i494f23_team25";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct metrics from the Ingredient_Quantity table
$sqlMetrics = "SELECT DISTINCT metric FROM Ingredient_Quantity";
$resultMetrics = $conn->query($sqlMetrics);

$options = array();

if ($resultMetrics->num_rows > 0) {
    while ($rowMetric = $resultMetrics->fetch_assoc()) {
        $options[] = $rowMetric["metric"];
    }
}

// Return JSON response
echo json_encode($options);

// Close the database connection
$conn->close();
?>
