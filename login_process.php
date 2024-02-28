<?php
    session_start();

    $jsonPayload = file_get_contents('php://input');
    $data = json_decode($jsonPayload, true);

    // creating a session with GoogleID, FirstName, LastName
    $_SESSION['user_id'] = $data['sub'];
    $_SESSION['first_name'] = $data['given_name'];
    $_SESSION['last_name'] = $data['family_name'];
    $_SESSION['email'] = $data['email'];

    $servername = "db.luddy.indiana.edu";
    $username = "i494f23_team25";
    $password = "my+sql=i494f23_team25";
    $dbname = "i494f23_team25";

    // Create database connection 
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) { die("Connection failed: " . mysqli_connect_error());}

    // check if user is already stored in the database
    $sql = "SELECT COUNT(*) as count FROM User WHERE GoogleAuth = '{$_SESSION['user_id']}'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];

    // If user is already in the database -> update FirstName and LastName
    if ($count > 0) {
        $sql2 = "UPDATE User SET FirstName = '{$_SESSION['first_name']}', LastName = '{$_SESSION['last_name']}' WHERE GoogleAuth = '{$_SESSION['user_id']}'";
        $result2 = mysqli_query($conn, $sql2);
    // If not, add user into User table
    } else{
        $sql = "INSERT INTO User (GoogleAuth, FirstName, LastName, Email) VALUES
        ('{$_SESSION['user_id']}', '{$_SESSION['first_name']}', '{$_SESSION['last_name']}', '{$_SESSION['email']}')";
        $result = mysqli_query($conn, $sql);
    }
?>
