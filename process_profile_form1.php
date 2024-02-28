<?php
session_start();

// Database connection
$conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userIDQuery = "SELECT UserID FROM User WHERE Email = '" . $_SESSION['email'] . "'";
    $userIDResult = mysqli_query($conn, $userIDQuery);
    
    if ($userIDResult) {
        $userIDRow = mysqli_fetch_assoc($userIDResult);
        $user_id = $userIDRow['UserID'];
        
        $height = isset($_POST['height']) ? $_POST['height'] : 0;
        $updateHeightQuery = "UPDATE User SET Height = $height WHERE UserID = $user_id";
        if (mysqli_query($conn, $updateHeightQuery)) {
            echo "Weight data updated successfully<br>";
        } else {
            echo "Error updating weight record: " . mysqli_error($conn) . "<br>";
        }

     

        $weight = isset($_POST['weight']) ? $_POST['weight'] : 0;
        $updateWeightQuery = "UPDATE User SET Weight = $weight WHERE UserID = $user_id";

        if (mysqli_query($conn, $updateWeightQuery)) {
            echo "Weight data updated successfully<br>";
        } else {
            echo "Error updating weight record: " . mysqli_error($conn) . "<br>";
        }
        header("Location: profile.php");
exit(); // Make sure to exit after sending the header to prevent further execution
    } else {
        echo "Error fetching userID: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "User not logged in";
}

mysqli_close($conn);
?>