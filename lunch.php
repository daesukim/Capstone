<?php
$conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
$sql = "SELECT RecipeID, Name, url FROM Recipe WHERE meal_type = 2";
$result = mysqli_query($conn, $sql);
?>