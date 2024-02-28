<html>
    <head>
        <!-- Nav Bar Style Sheet -->
        <link rel="stylesheet" href="css/profile3.css">
        <!-- Nav Bar Google Icon -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
        <!-- Google Font -->
        <!-- Includes Inter Medium (font-weight: 500) and Inter Bold (font-weight: 700) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
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
                    <li><a href="index.html">Landing Page</a></li>
                    <li><a href="team.html">About Us</a></li>
                    <li><a href="project.html">About the Project</a></li>
                    <li><a href="video.html">Promotional Video</a></li>
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
            <div class="file-input">
                <div class="file_first">
                <input type="file" id="file" class="file">
                <label for="file">+<p class="file-name"></p></label>
                <button id="logout">Log out</button>
                <script>
                    document.getElementById('logout').addEventListener('click', function() {
                        window.location.href = 'logout.php';
                    });
                </script>
            </div>
            <div class="file_descript">
                <?php
                session_start();
                if (isset($_SESSION['user_id'])) {
                    echo "<h1>" . $_SESSION['first_name'] . "</h1>";
                    echo "<p>" . $_SESSION['email'] . "</p>";
                }
                else{
                    echo "<h1> Name </h1>";
                    echo "<p class='show_email'>Email</p>";
                }

                ?>
                <p class="show_profile_info">Height|weight</p>
            </div>
        </div>
        <div class="profile_container1">
            <div class="profile_topic">
                <button class="toggle2" type='button' onclick="toggleText2()"><h3>PREFERENCES</h3></button>
                <button class="toggle3" type='button' onclick="toggleText3()"><h3>PROFILE SETTING</h3></button>
            </div>
            <div class="selection_conatainer" id="demo2" style="display:none">
                    <!-- form starts here -->
                    <form action="process_profile_form.php" method="post">
                        <div class="preference_section">
                            <h2>DIETARIES</h2>
                            <h3>Add Dietaries</h3>
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
                            <input type="text" name="selectedPreferences" id="selectedPreferences" value="">
                        </div>
                        <div class="allergy_section">
                            <h2>ALLERGIES</h2>
                            <h3>Add Allergies</h3>
                            <div class="element_container">
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
                            <input type="text" name="selectedAllergy" id="selectedAllergy" value="">
                        </div>
                        <div class="household_section">
                            <h2>NUMBER OF HOUSEHOLD</h2>
                            <div class="household_input">
                                <input type="number" id="household" name="tentacles" min="1" max="100" value="1" />
                            </div>
                        </div>
                        <button class="savesub" type="submit">SAVE & SUBMIT</button>
                    </form>
                </div>
            </div>
            <div class="profile_section" id='demo3' style='display: none'>
                <form action="process_profile_form.php" method="post">
                    <div class="height">
                        <label for="heightInput">Enter Your Height</label>
                        <input type="text" id="heightInput" placeholder="Enter height in feet">
                        <button class="convert" onclick="convertHeightToCm(event)">Convert Height</button>
                        <div id="heightResult"></div>
                    </div>
                    <div class="weight">
                        <label for="weightInput">Enter Your Weight</label>
                        <input type="text" id="weightInput" placeholder="Enter weight in pounds">
                        <button class="convert" onclick="convertWeightToKg(event)">Convert Weight</button>
                        <div id="weightResult"></div>
                    </div>
                    <button class="savesub" id="profileid" type="submit">SAVE&SUBMIT</button>
                </form>
            </div>
        </div>

        <script>
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
                event.preventDefault(event);
                var feetValue = parseFloat(document.getElementById('heightInput').value);

                // Convert height to centimeters
                if (!isNaN(feetValue)) {
                    var cmValue = feetValue * 30.48;  // 1 foot = 30.48 cm
                    document.getElementById('heightResult').textContent = feetValue + ' feet is equal to ' + cmValue.toFixed(2) + ' cm';
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