<?php
// Start session
session_start();

// Database connection
$conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['user_id'])) {
    $userIDQuery = "SELECT UserID FROM User WHERE Email = '" . $_SESSION['email'] . "'";
    $userIDResult = mysqli_query($conn, $userIDQuery);
    
    if ($userIDResult) {
        $userIDRow = mysqli_fetch_assoc($userIDResult);
        $user_id = $userIDRow['UserID'];

        // Process selected dietary preferences
        if (isset($_POST['selectedPreferences'])) {
            $selectedPreferencesArray = explode(',', $_POST['selectedPreferences']);
            $dietary_delete = "DELETE FROM user_dietary WHERE userID = $user_id";
                if (mysqli_query($conn, $dietary_delete)) {
                    echo "Dietary data deleted<br>";
                } else {
                    echo "Error deleting dietary preference: " . mysqli_error($conn) . "<br>";
                }
            foreach ($selectedPreferencesArray as $preference) {
                $dietary_insert = "INSERT INTO user_dietary (userID, dietaryID) VALUES ($user_id, $preference)";
                if (mysqli_query($conn, $dietary_insert)) {
                    echo "Dietary data inserted successfully<br>";
                } else {
                    echo "Error inserting dietary preference: " . mysqli_error($conn) . "<br>";
                }
            }
        }

        // Process selected allergies
        if (isset($_POST['selectedAllergy'])) {
            $selectedAllergyArray = explode(',', $_POST['selectedAllergy']);
            $allergy_delete = "DELETE FROM user_allergy WHERE userID = $user_id";
                if (mysqli_query($conn, $allergy_delete)) {
                    echo "Dietary data deleted<br>";
                } else {
                    echo "Error deleting dietary preference: " . mysqli_error($conn) . "<br>";
                }
                foreach ($selectedAllergyArray as $allergy) {
                    $allergy_insert = "INSERT INTO user_allergy (userID, allergyID) VALUES ($user_id, $allergy)";
                    if (mysqli_query($conn, $allergy_insert)) {
                        echo "Allergy data inserted successfully<br>";
                    } else {
                        echo "Error inserting dietary preference: " . mysqli_error($conn) . "<br>";
                    }
            }
        }

        // Process number of households
        $numHouseholds = isset($_POST['tentacles']) ? $_POST['tentacles'] : 0;
$updateQuery = "UPDATE User SET Num_CookingFor = '$numHouseholds' WHERE UserID = $user_id";

if (mysqli_query($conn, $updateQuery)) {
    echo "Household data updated successfully<br>";
} else {
    echo "Error updating household record: " . mysqli_error($conn) . "<br>";
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
