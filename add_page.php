<html>
<head>
    <title>
        View Individual Recipe
    </title>

    <!-- Style Sheet -->
    <link rel="stylesheet" href="css/add_page.css">
    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    ?>

    <!-- Nav Bar -->
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

    <!-- Back Button -->
    <span class="material-symbols-outlined hvr-grow backButton" onclick="history.go(-1);">arrow_back </span>

    <!-- Page Content -->
    <?php
        $servername = "db.luddy.indiana.edu";
        $username = "i494f23_team25";
        $password = "my+sql=i494f23_team25";
        $dbname = "i494f23_team25";
        $con = mysqli_connect($servername, $username, $password, $dbname);

        if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error()); }

        // Need to pass in recipe ID in a form with input name being recipeToView
        if (isset($_GET['menu_id'])) {
            $id = $_GET['menu_id'];
        }

        $name_sql = "SELECT Name AS 'name' FROM Recipe WHERE RecipeID = $id";

        $name_res = mysqli_query($con, $name_sql);
        $name = mysqli_fetch_assoc($name_res);

        echo '<h1 class="recipeName">' . $name["name"] . '</h1>';

    ?>
    <div class="container">
        <div class = "left">
            <?php
            $servername = "db.luddy.indiana.edu";
            $username = "i494f23_team25";
            $password = "my+sql=i494f23_team25";
            $dbname = "i494f23_team25";
            $con = mysqli_connect($servername, $username, $password, $dbname);

            if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error()); }

            // Need to pass in recipe ID in a form with input name being recipeToView
            if (isset($_GET['menu_id'])) {
                $id = $_GET['menu_id'];
            }


            $recipe_sql = "SELECT r.Name AS 'recipeName', r.cuisine_type AS 'cuisineType', r.servings AS 'servings', r.calories AS 'calories', r.meal_type AS 'mealType', r.Url AS 'url'
            FROM Recipe AS r
            WHERE r.RecipeID = $id";

            $recipe_res = mysqli_query($con, $recipe_sql);

            $dietary_sql = "SELECT DISTINCT dp.Preference AS 'preference'
            FROM Recipe AS r
            JOIN recipe_dietary AS rd ON r.RecipeID = rd.recipeID
            JOIN Dietary_Preference AS dp ON rd.dietaryID = dp.PreferenceID
            WHERE r.RecipeID = $id";

            $dietary_res = mysqli_query($con, $dietary_sql);
            if (mysqli_num_rows($dietary_res) > 0) {
                $dietary = array();
                while($row = mysqli_fetch_assoc($dietary_res)) {
                    array_push($dietary, $row['preference']);
                }
            }

            $allergy_sql = "SELECT DISTINCT a.Allergy AS 'allergy'
            FROM Recipe AS r
            JOIN recipe_allergy AS ra ON r.RecipeID = ra.recipeID
            JOIN Allergy AS a ON a.AllergyID = ra.allergyID
            WHERE r.RecipeID = $id";

            $allergy_res = mysqli_query($con, $allergy_sql);
            if (mysqli_num_rows($allergy_res) > 0) {
                $allergy = array();
                while($row = mysqli_fetch_assoc($allergy_res)) {
                    array_push($allergy, $row['allergy']);
                }
            }

            $recipe = mysqli_fetch_assoc($recipe_res);
            echo '<div class="recipe">
                    <img class="recipeImg" src="' . $recipe['url'] . '" alt="">
                        <div class="recipe_info">
                            <div class = "type_allergen">';
                                if (mysqli_num_rows($dietary_res) > 0) {
                                    if (count($dietary) > 1) {
                                        echo '<p class="lightText">';
                                        for ($i = 0; $i < count($dietary) - 1; $i++) {
                                            if ($dietary[$i] != 'No Preference') {
                                                echo $dietary[$i] . ', ';
                                            }
                                        }
                                        if ($dietary[count($dietary) - 1] != 'No Preference') {
                                            echo $dietary[count($dietary) - 1];
                                        }
                                        echo '</p>';
                                    } else {
                                        if ($dietary[count($dietary) - 1] != 'No Preference') {
                                            echo '<p class="lightText">' . $dietary[count($dietary) - 1] . '</p>';
                                        }
                                    }
                                }
                                if (mysqli_num_rows($allergy_res) > 0) {
                                    echo '<p class="italicText"> Contains: ';
                                    if (count($allergy) > 1) {
                                        for ($i = 0; $i < count($allergy) - 1; $i++) {
                                            echo $allergy[$i] . ', ';
                                        }
                                        echo $allergy[count($allergy) - 1];
                                    } else {
                                        echo $allergy[count($allergy) - 1];
                                    }
                                    echo '</p>';
                                }
                        echo '</div>
                            <div class = "serve_calorie">
                                <p class="lightText">' . $recipe['calories'] . ' calories </p>
                                <p class="lightText">' . $recipe['servings'] . ' servings </p>
                            </div>
                        </div>'
                        ?>
                  </div>
                  <?php
                    $servername = "db.luddy.indiana.edu";
                    $username = "i494f23_team25";
                    $password = "my+sql=i494f23_team25";
                    $dbname = "i494f23_team25";
                    $con = mysqli_connect($servername, $username, $password, $dbname);

                    if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error()); }

                    // Need to pass in recipe ID in a form with input name being recipeToView
                    if (isset($_GET['menu_id'])) {
                        $id = $_GET['menu_id'];
                    }

                    echo '<div class="ing">
                    <br>
                    <h2>Ingredients</h2>';

                    $get_ingredients = "SELECT CONCAT(IF(iq.quantity IS NULL, '', CONCAT(ROUND(iq.quantity, 2), ' ')), IF(iq.metric IS NULL, '', CONCAT(iq.metric, ' ')), i.Name) AS ingredient
                    FROM Ingredient AS i 
                    JOIN Ingredient_Quantity AS iq ON i.IngredientID = iq.IngredientID
                    JOIN Recipe AS r ON iq.recipeID = r.RecipeID
                    WHERE r.RecipeID = " . $id;

                    $ingredients = mysqli_query($con, $get_ingredients);
                    if (mysqli_num_rows($ingredients) > 0) {
                        echo '<ul>'; 
                        while ($ingredient = mysqli_fetch_assoc($ingredients)) {
                            echo '<li class="ulItem">' . $ingredient['ingredient'] . '</li>';
                        }
                        echo '</ul>'; 
                    }

                    echo '</div><br>';
                ?>
        </div>
        <div class = "right">
            <?php
            $servername = "db.luddy.indiana.edu";
            $username = "i494f23_team25";
            $password = "my+sql=i494f23_team25";
            $dbname = "i494f23_team25";
            $con = mysqli_connect($servername, $username, $password, $dbname);

            if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error()); }

            // Need to pass in recipe ID in a form with input name being recipeToView
            if (isset($_GET['menu_id'])) {
                $id = $_GET['menu_id'];
            }

            echo '<div class="step">
            <h2>Steps</h2>';

            $get_steps = "SELECT rs.StepNumber, rs.Step AS 'step'
            FROM Recipe AS r
            JOIN Recipe_Steps AS rs ON rs.recipeID = r.RecipeID
            WHERE r.RecipeID = " . $id . "
            ORDER BY rs.StepNumber";

            $steps = mysqli_query($con, $get_steps);
            if (mysqli_num_rows($steps) > 0) {
                echo '<ol>'; 
                while ($step = mysqli_fetch_assoc($steps)) {
                    echo '<li class="olItem">' . $step['step'] . '</li>';
                }
                echo '</ol>'; 
            }
            echo '</div>';
            ?>
            <div class = "meal_plan_add">
                <h2>Add to Meal Plan</h2>
                <ol>
                    <li class="olItem"> <a href = "#" id="showCalendar">Click</a> to Create a meal plan </li>
                    <li class="olItem">Select a meal plan</li>
                    <form action="add_page_process.php?menuID=<?php echo $_GET['menu_id']; ?>" method="POST">
                        <select name="meal_plan_id">
                            <?php
                            $servername = "db.luddy.indiana.edu";
                            $username = "i494f23_team25";
                            $password = "my+sql=i494f23_team25";
                            $dbname = "i494f23_team25";
                            $con = mysqli_connect($servername, $username, $password, $dbname);
                            if (!$con) { die("Connection failed: " . mysqli_connect_error());}
                            $sql = "SELECT mp.PlanID AS PlanID, mp.startDate  AS startDate, mp.endDATE AS endDate
                                    FROM Meal_Plan AS mp
                                    JOIN user_mealplan AS ump ON mp.PlanID = ump.mealPlanID
                                    JOIN User AS u ON u.UserID = ump.userID
                                    WHERE u.GoogleAuth = " . $_SESSION['user_id'] . 
                                    " ORDER BY startDate DESC";
                            $result = mysqli_query($con, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $mealPlan_id=$row['PlanID'];
                                    $startDate=$row['startDate'];
                                    $endDate = $row['endDate'];
                                    $concatenatedDates = $startDate . ' / ' . $endDate;
                                    echo  "<option value='". $mealPlan_id. "'>" . $concatenatedDates. "</option>";
                                }
                            }
                            else{
                                echo  "<option value = 'none'> No Meal Plan Exists </option>";
                            }
                            ?>
                        </select>
                    <li class="olItem"> Choose days for
                        <?php
                        $servername = "db.luddy.indiana.edu";
                        $username = "i494f23_team25";
                        $password = "my+sql=i494f23_team25";
                        $dbname = "i494f23_team25";
                        $con = mysqli_connect($servername, $username, $password, $dbname);
                        if (!$con) { die("Connection failed: " . mysqli_connect_error());}
                        if (isset($_GET['menu_id'])) {
                            $id = $_GET['menu_id'];
                        }
                        $sql = "SELECT meal_type FROM Recipe WHERE RecipeID = " . $id;
                        $result = mysqli_query($con, $sql);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $_meal_typeID = $row['meal_type'];
                            if ($_meal_typeID == 1){
                                echo "Breakfast";
                            }
                            else if ($_meal_typeID == 2){
                                echo "Lunch";
                            }
                            else if ($_meal_typeID == 3){
                                echo "Dinner";
                            }
                        }
                        ?>
                    </li>
                    <div class="checkbox-row">
                        <div class="checkbox-group">
                            <input type="checkbox" name="days[]" value="1"><p>Monday</p>
                            <input type="checkbox" name="days[]" value="2"><p>Tuesday</p>
                            <input type="checkbox" name="days[]" value="3"><p>Wednesday</p>
                        </div>
                    </div>

                    <div class="checkbox-row">
                        <div class="checkbox-group">
                            <input type="checkbox" name="days[]" value="4"><p>Thursday</p>
                            <input type="checkbox" name="days[]" value="5"><p>Friday</p>
                            <input type="checkbox" name="days[]" value="6"><p>Saturday</p>
                            <input type="checkbox" name="days[]" value="0"><p>Sunday</p>
                        </div>
                    </div>
                    <button type="submit" class = "button">Submit</button></div>
                    </form>
                </ol>
            </div>
        </div>
    </div>
    <?php if(isset($_SESSION['error_message'])): ?>
        <script>
            alert("<?php echo $_SESSION['error_message']; ?>");
            <?php unset($_SESSION['error_message']); ?>
        </script>
    <?php endif; ?>
    
    <div class = "hidden_section hidden">
        <div class = "rec_info">
            <div class = 'close' id = 'close'></div>
            <form action="meal_plan_process.php?menuID=<?php echo $_GET['menu_id']; ?>" method="POST">
                <label for="start_date">Select a start date:</label><br>
                <input type="date" id="start_date" name="start_date" required><br>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('showCalendar').addEventListener('click', function(event) {
                console.log("show Calendar clicked");
                var content = document.querySelector('.hidden_section');
                    if (content) {
                        content.classList.remove('hidden');
                        content.classList.add('visible');
                    } else {
                        console.error("Element with class 'hidden_section' not found.");
                    }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(event) {
                if (event.target.matches('.close')) {
                    console.log("close button clicked");
                    var content = document.querySelector('.hidden_section');
                    if (content) {
                        content.classList.remove('visible');
                        content.classList.add('hidden');
                    } else {
                        console.error("error");
                    }
                }
            });
        });

    </script>

</body>
</html>

