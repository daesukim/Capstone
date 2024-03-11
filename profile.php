<?php
// Start session

// Database connection
$conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_close($conn);
?>

<html>
    <head>
        <!-- Nav Bar Style Sheet -->
        <link rel="stylesheet" href="css/profile.css">
        <!-- Nav Bar Google Icon -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <!-- Google Font -->
        <!-- Includes Inter Medium (font-weight: 500) and Inter Bold (font-weight: 700) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">

    </head>
    <body>
        <div class="bar">
            <ul>
                <div class="items">
                    <li>
                        <a href="index.html"><span class="icon material-symbols-outlined">grocery</span></a>
                    </li>  
                    <li class="gg"> GreenGrocer </li>
                </div>
            </ul>    
            <!-- Side Navigation -->
            <nav id="mySidenav" class="sidenav">
                <ul>
                <li><a class="closebtn">&times;</a></li>
                <li><a href="landing.php">Home</a></li>
                <li><a href="view_recipe.php">View Recipes</a></li>
                <li><a href="meal_plan.php">My Meal Plan</a></li>
                <li><a href="calorie.php">Calorie Conscious Meal Plan</a></li>
                <li><a href="profile.php">Profile Settings</a></li>
                <li><a href="recipe.php">Recipe Creation</a></li>
                </ul>
            </nav>
            <!-- Open Side Navigtaion -->
            <div class="openbtn">
                <span class="material-symbols-outlined menu-button">menu</span>
            </div>
            <div class="all-over-bkg"></div>
        </div>
        <!-- Nav Bar Javascript -->
        <script src="js/nav.js"></script>

        <!-- start of the content -->
        <div class="profile_container">
            <!--
            <div class="file-input">
                <div class="file_first">
                <input type="file" id="file" class="file">
                <label for="file">+<p class="file-name"></p></label> </div>
-->
               
           
            <div class="file_descript">
            <?php
            $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");

            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }

            session_start();
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
                //$_SESSION['user_id'] = 1234567;
            }

            if (isset($_SESSION['user_id'])) {
                $userEmail = $_SESSION['email'];

                $userGenderQuery = "SELECT Gender FROM User WHERE Email = '$userEmail'";
                $userGenderResult = mysqli_query($conn, $userGenderQuery);
                $userGender = mysqli_fetch_assoc($userGenderResult)['Gender'];

                $userAgeQuery = "SELECT Age FROM User WHERE Email = '$userEmail'";
                $userAgeResult = mysqli_query($conn, $userAgeQuery);
                $userAge = mysqli_fetch_assoc($userAgeResult)['Age'];

                // Query to get user height
                $userHeightQuery = "SELECT Height FROM User WHERE Email = '$userEmail'";
                $userHeightResult = mysqli_query($conn, $userHeightQuery);
                $userHeight = mysqli_fetch_assoc($userHeightResult)['Height'];

                // Query to get user weight
                $userWeightQuery = "SELECT Weight FROM User WHERE Email = '$userEmail'";
                $userWeightResult = mysqli_query($conn, $userWeightQuery);
                $userWeight = mysqli_fetch_assoc($userWeightResult)['Weight'];

                $userActivityQuery = "SELECT activity_level FROM User WHERE Email = '$userEmail'";
                $userActivityResult = mysqli_query($conn, $userActivityQuery);
                $userActivity = mysqli_fetch_assoc($userActivityResult)['activity_level'];

                echo "<h1>" . $_SESSION['first_name'] . "</h1>";
                echo "<p>" . $_SESSION['email'] . "</p>";
                echo "<p>". $userGender. " | ". "Age: ". $userAge. " | Height: " . $userHeight . " in | Weight: " . $userWeight . " lbs</p>";
                
                if ($userActivity == 1) {
                    echo "Activity Level: Moderate";
                } elseif ($userActivity == 2) {
                    echo "Activity Level: Lightly Active";
                } elseif ($userActivity == 3) {
                    echo "Activity Level: Moderately Active";
                } elseif ($userActivity == 4) {
                    echo "Activity Level: Very Active";
                } elseif ($userActivity == 5) {
                    echo "Activity Level: Extra Active";
                } else {
                    echo "Unknown activity level";
                }            
            } else {
                echo "<h1> Name </h1>";
                echo "<p class='show_email'>Email</p>";
                echo "<p class='show_profile_info'>Gender | Age | Height | Weight</p>";
                echo "<p class='show_profile_info'>Activity Level</p>";
            }

            mysqli_close($conn);
            ?>
            </div>
            <button id="logout">Log out</button>
                <script>
                    document.getElementById('logout').addEventListener('click', function() {
                        window.location.href = 'logout.php';
                    });
                </script>
        </div>
        <div class="profile_container1">
            <div class="profile_topic">
                <h3>PREFERENCES</h3>
            </div>
            <div class="selection_conatainer">
                    <!-- form starts here -->
                        <div class="preference_section">
                            <h2>DIETARIES</h2>
                            <h3>Dietaries</h3>
                            <div class="element_container" id="preferencesContainer">
                            <?php
                            $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Check if user's ID is stored in the session
                            $userIDQuery = "SELECT UserID FROM User WHERE Email = '" . $_SESSION['email'] . "'";
                                $userIDResult = mysqli_query($conn, $userIDQuery);
                                
                                if ($userIDResult) {
                                    $userIDRow = mysqli_fetch_assoc($userIDResult);
                                    $user_id = $userIDRow['UserID'];

                                // Query the user's selected preferences from the user_dietary table
                                $selectedPreferencesQuery = "SELECT dp.PreferenceID, dp.Preference
                                                            FROM Dietary_Preference dp
                                                            JOIN user_dietary ud ON dp.PreferenceID = ud.dietaryID
                                                            WHERE ud.userID = '$user_id'";
                                $selectedPreferencesResult = mysqli_query($conn, $selectedPreferencesQuery);

                                // Check if the query was successful
                                if ($selectedPreferencesResult) {
                                    while ($row = mysqli_fetch_assoc($selectedPreferencesResult)) {
                                        $_preferenceID = $row["PreferenceID"];
                                        $_preferenceName = $row["Preference"];

                                        // Display the selected preferences
                                        echo "<div class='circle selected'><p>" . $_preferenceName . "</p></div>";
                                    }
                                } else {
                                    echo "Error fetching selected preferences: " . mysqli_error($conn);
                                }
                            } else {
                                echo "User ID not found in session.";
                            }


                            mysqli_close($conn);
                            ?>

                        </div>
                        <input type="hidden" name="selectedPreferences" id="selectedPreferences" value="">

                        </div>
                        <div class="allergy_section">
                            <h2>ALLERGIES</h2>
                            <h3>Allergies</h3>
                            <div class="element_container" id="allergenContainer">
                            <?php
                            $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Check if user's ID is stored in the session
                            $userIDQuery = "SELECT UserID FROM User WHERE Email = '" . $_SESSION['email'] . "'";
                                $userIDResult = mysqli_query($conn, $userIDQuery);
                                
                                if ($userIDResult) {
                                    $userIDRow = mysqli_fetch_assoc($userIDResult);
                                    $user_id = $userIDRow['UserID'];

                                // Query the user's selected preferences from the user_dietary table
                                $selectedAllergyQuery = "SELECT a.AllergyID, a.Allergy
                                                            FROM Allergy a
                                                            JOIN user_allergy ua ON a.AllergyID = ua.allergyID
                                                            WHERE ua.userID = '$user_id'";
                                $selectedAllergyResult = mysqli_query($conn, $selectedAllergyQuery);

                                // Check if the query was successful
                                if ($selectedAllergyResult) {
                                    while ($row = mysqli_fetch_assoc($selectedAllergyResult)) {
                                        $_allergyID = $row["AllergyID"];
                                        $_AllergyName = $row["Allergy"];

                                        // Display the selected preferences
                                        echo "<div class='circle selected'><p>" . $_AllergyName . "</p></div>";
                                    }
                                } else {
                                    echo "Error fetching selected Allergy: " . mysqli_error($conn);
                                }
                            } else {
                                echo "User ID not found in session.";
                            }


                            mysqli_close($conn);
                            ?>
                            </div>
                            <input type="hidden" name="selectedAllergy" id="selectedAllergy" value="">
                        </div>
                        <div class="household_section">
                            <h2>Serving Number</h2>
                            <div class="household_input">
                            <?php
                            $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }
                                session_start();

                                if (isset($_SESSION['user_id'])) {
                                    $userEmail = $_SESSION['email'];

                                    // Query to get user height
                                    $userCookQuery = "SELECT Num_CookingFor FROM User WHERE Email = '$userEmail'";
                                    $userCookResult = mysqli_query($conn, $userCookQuery);
                                    $userCook = mysqli_fetch_assoc($userCookResult)['Num_CookingFor'];

                                    echo "<p style='font-size: 36px;'>" . $userCook . "</p>";  
                                } else {
                                        echo "<p> You have not select the household number</p>";
                                    }
                                    mysqli_close($conn);
                                ?>
                                <!--
                                <input type="number" id="household" name="tentacles" min="1" max="100" value="1" />
                                -->
                            </div>
                        </div>
                       <button class="savesub"><a href="profile_edit.php" style="text-decoration: none; color: white;">Edit My Setting</a></button>
                </div>
            </div>
        </div>

        <script>
            function changeColor(circle) {
                console.log("clicked");
                var isClicked = circle.classList.contains('selected');
                circle.classList.toggle('selected');
                var borderColor = isClicked ? '10px solid #d9d9d9' : '10px solid red';
                var paragraph = circle.querySelector('p');
                paragraph.style.border = borderColor;
                updateSelectedPreferences();
            }

            function allergyEvent(element){
                console.log("clicked");
                var isClicked = element.classList.contains('selected');
                element.classList.toggle('selected');
                var borderColor = isClicked ? '10px solid #d9d9d9' : '10px solid red';
                var paragraph = element.querySelector('p');
                paragraph.style.border = borderColor;
                updateSelectedAllergy();
            }

            function updateSelectedPreferences() {
                var selectedPreferences = document.querySelectorAll('.circle.selected');
                var preferencesArray = [];

                for (var i = 0; i < selectedPreferences.length; i++) {
                    preferencesArray.push(selectedPreferences[i].getAttribute('data-preference'));
                }
                document.getElementById('selectedPreferences').value = preferencesArray.join(',');
            }

            function updateSelectedAllergy() {
                var selectedAllergy = document.querySelectorAll('.allergen.selected');
                var AllergyArray = [];

                for (var i = 0; i < selectedAllergy.length; i++) {
                    AllergyArray.push(selectedAllergy[i].getAttribute('data-preference'));
                }
                document.getElementById('selectedAllergy').value = AllergyArray.join(',');
            }



            function convertHeightToCm(event) {
    event.preventDefault();
    var inchesValue = parseFloat(document.getElementById('heightInput').value);

    // Convert height to centimeters
    if (!isNaN(inchesValue)) {
        var cmValue = inchesValue * 2.54;  // 1 inch = 2.54 cm
        document.getElementById('heightResult').textContent = inchesValue + ' inches is equal to ' + cmValue.toFixed(2) + ' cm';
    } else {
        document.getElementById('heightResult').textContent = 'Please enter a valid height.';
    }
}

            function convertWeightToKg(event) {
                event.preventDefault(event);
                var poundsValue = parseFloat(document.getElementById('weightInput').value);

                // Convert weight to kilograms
                if (!isNaN(poundsValue)) {
                    var kgValue = poundsValue * 0.453592;
                    document.getElementById('weightResult').textContent = poundsValue + ' pounds is equal to ' + kgValue.toFixed(2) + ' kg';
                } else {
                    document.getElementById('weightResult').textContent = 'Please enter a valid weight.';
                }
            }
        </script>
    </body>
</html>