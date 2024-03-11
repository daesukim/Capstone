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

            if (isset($_SESSION['user_id'])) {
                $userEmail = $_SESSION['email'];

                // Query to get user height
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
                <button class="toggle2" type='button' onclick="toggleText2()"><h3>PREFERENCES</h3></button>
                <button class="toggle3" type='button' onclick="toggleText3()"><h3>PROFILE SETTING</h3></button>
            </div>
            <div class="selection_conatainer" id="demo2" style="display:none">
                    <!-- form starts here -->
                        <div class="preference_section">
                        <form action="process_profile_form.php" method="post">
                            <h2>DIETARIES</h2>
                            <h3>Dietaries<span style="color:#6ED184;">+</span></h3>
                            <div class="element_container" id="preferencesContainer">
                                <?php
                                $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }
                                $sql = "SELECT PreferenceID, Preference FROM Dietary_Preference";
                                $result = mysqli_query($conn, $sql);

                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $_preferenceID = $row["PreferenceID"];
                                        $_preferenceName = $row["Preference"];
                                        echo "<div class='circle' onclick='changeColor(this)' data-preference='" . $_preferenceID . "'><p>" . $_preferenceName . "</p></div>";
                                    }
                                } else {
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?> 
                            </div>
                            <input type="hidden" name="selectedPreferences" id="selectedPreferences" value="">
                        </div>
                        <div class="allergy_section">
                            <h2>ALLERGIES</h2>
                            <h3>Allergies<span style="color:#6ED184;;">+</span></h3>
                            <div class="element_container" id="allergenContainer">
                                <?php
                                $conn = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25", "my+sql=i494f23_team25", "i494f23_team25");
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }
                                $sql2 = "SELECT AllergyID, Allergy FROM Allergy";
                                $result2 = mysqli_query($conn, $sql2);

                                if ($result2) {
                                    while ($row = mysqli_fetch_assoc($result2)) {
                                        $_AllergyID = $row["AllergyID"];
                                        $_Allergy = $row["Allergy"];
                                        echo "<div class='allergen' onclick='allergyEvent(this)' data-preference='" . $_AllergyID . "'><p>" . $_Allergy . "</p></div>";
                                    }
                                } else {
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>
                            </div>
                            <input type="hidden" name="selectedAllergy" id="selectedAllergy" value="">
                        </div>
                        <div class="household_section">
                            <h2>Serving Number</h2>
                            <div class="household_input">
                                <input type="number" id="household" name="tentacles" min="1" max="100" value="1" />
                            </div>
                        </div>
                        <button class="savesub" id="preferencesubmit" type="submit">SAVE & SUBMIT</button>
                    </form>
                </div>
            </div>
            <div class="profile_section" id='demo3' style='display: none'>
                <form action="process_profile_form1.php" method="post">
                    <div class="height">
                        <label for="heightInput">Enter Your Height</label>
                        <input type="text" id="heightInput" name="height" placeholder="Enter height in inches" pattern="[0-9]+([\.,][0-9]+)?" />
                        <button class="convert" onclick="convertHeightToCm(event)">Convert Height</button>
                        <div id="heightResult"></div>
                    </div>
                    <div class="weight">
                        <label for="weightInput">Enter Your Weight</label>
                        <input type="text" id="weightInput" name="weight" placeholder="Enter weight in pounds" pattern="[0-9]+([\.,][0-9]+)?" />
                        <button class="convert" onclick="convertWeightToKg(event)">Convert Weight</button>
                        <div id="weightResult"></div>
                    </div>
                    <div class="gender">
                        <label for="genderInput">Select Your Gender</label><br>
                        <input type="radio" class="radio_btn" style="width: 20px; height: 20px;" id="genderInput" name="gender" value="M"/>
                        <label for="genderInput" class="radio_label" style="font-size: 20px;">M</label>
                        <input type="radio" class="radio_btn" style="width: 20px; height: 20px;" id="genderInput1" name="gender" value="F"/>
                        <label for="genderInput1" class="radio_label" style="font-size: 20px;">F</label>    
                    </div>
                    <div class="age">
                        <label for="ageInput">Enter Your Age</label>
                        <input type="number" id="ageInput" name="age" placeholder="Enter your Age" pattern="[0-9]+([\.,][0-9]+)?"/>
                    </div>
                    <div class="activity">
                        <label for="activity">Select Your Activity Level</label><br>
                        <input type="radio" class="radio" style="width: 20px; height: 20px;"id="level1" name="activity" value="1"/>
                        <label for="level1" class="radio_label" style="font-size: 20px;">Sedentary</label>
                        
                        <input type="radio" class="radio" style="width: 20px; height: 20px;" id="level2" name="activity" value="2"/>
                        <label for="level2" class="radio_label" style="font-size: 20px;">Lightly Active</label>
                        
                        <input type="radio" class="radio" style="width: 20px; height: 20px;" id="level3" name="activity" value="3"/>
                        <label for="level3" class="radio_label" style="font-size: 20px;">Moderately Active</label>
                        
                        <input type="radio" class="radio" style="width: 20px; height: 20px;" id="level4" name="activity" value="4"/>
                        <label for="level4" class="radio_label" style="font-size: 20px;">Very Active</label>
                        
                        <input type="radio" class="radio" style="width: 20px; height: 20px;" id="level5" name="activity" value="5"/>
                        <label for="level5" class="radio_label" style="font-size: 20px;">Extra Active</label> 
                    </div>
                    <button class="savesub" id="profilesubmit" type="submit">SAVE&SUBMIT</button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var text2 = document.getElementById("demo2");
                var text3 = document.getElementById("demo3");
                text2.style.display = "block";
                text3.style.display = "none";
            });
            function toggleText2() {
                var text = document.getElementById("demo2");
                var text3 = document.getElementById("demo3");

                if (text.style.display === "none") {
                    text.style.display = "block";
                    text3.style.display = "none";  // Hide text3 when showing text2
                } else {
                    text.style.display = "none";
                }
            }

            function toggleText3() {
                var text = document.getElementById("demo3");
                var text2 = document.getElementById("demo2");

                if (text.style.display === "none") {
                    text.style.display = "block";
                    text2.style.display = "none";
                } else {
                    text.style.display = "none";
                }
            }

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