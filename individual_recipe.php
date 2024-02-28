<html>
<head>
    <title>
        View Individual Recipe
    </title>

    <!-- Style Sheet -->
    <link rel="stylesheet" href="css/individual_recipe_styles.css">
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

    <!-- Back Button -->
    <span class="material-symbols-outlined hvr-grow backButton" onclick="history.go(-1);">arrow_back </span>

    <!-- Page Content -->
    <?php
        $servername = "db.luddy.indiana.edu";
        $username = "i494f23_team25";
        $password = "my+sql=i494f23_team25";
        $dbname = "i494f23_team25";
        $con = mysqli_connect($servername, $username, $password, $dbname);

        if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error); }

        // Need to pass in recipe ID in a form with input name being recipeToView
        $id = mysqli_real_escape_string($con, $_POST['recipeToView']);

        $name_sql = "SELECT Name AS 'name' FROM Recipe WHERE RecipeID = $id";

        $name_res = mysqli_query($con, $name_sql);
        $name = mysqli_fetch_assoc($name_res);

        echo '<h1 class="recipeName">' . $name["name"] . '</h1>';

    ?>
    <div class="container">
        <div class = "left">
            <?php
	    // Sessions
            session_start();
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit();
            }  	

            $servername = "db.luddy.indiana.edu";
            $username = "i494f23_team25";
            $password = "my+sql=i494f23_team25";
            $dbname = "i494f23_team25";
            $con = mysqli_connect($servername, $username, $password, $dbname);

            if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error); }

            // Need to pass in recipe ID in a form with input name being recipeToView
            $id = mysqli_real_escape_string($con, $_POST['recipeToView']);

            // Get number of people user is cooking for
            $num_cook_sql = "SELECT IFNULL(Num_CookingFor, 1) AS 'num_cook' FROM User WHERE GoogleAuth = " . $_SESSION['user_id'];
            $num_cook_res = mysqli_query($con, $num_cook_sql);
            $num_cook_row = mysqli_fetch_assoc($num_cook_res);
            $num_cook = $num_cook_row['num_cook'];	    

            $recipe_sql = "SELECT r.Name AS 'recipeName', r.cuisine_type AS 'cuisineType', r.servings AS 'servings', CEILING((r.calories / r.servings) * " . $num_cook . ") AS 'calories', r.meal_type AS 'mealType', r.Url AS 'url'
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
                                <p class="lightText">' . $num_cook . ' servings </p>
                            </div>
                        </div>'
                        ?>
                  </div>
        </div>
        <div class = "right">
            <?php
            $servername = "db.luddy.indiana.edu";
            $username = "i494f23_team25";
            $password = "my+sql=i494f23_team25";
            $dbname = "i494f23_team25";
            $con = mysqli_connect($servername, $username, $password, $dbname);

            if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error); }

            // Need to pass in recipe ID in a form with input name being recipeToView
            $id = mysqli_real_escape_string($con, $_POST['recipeToView']);

            echo '<div class="ing">
            <br>
            <h2>Ingredients</h2>';

            // Get number of people user is cooking for
            $num_cook_sql = "SELECT IFNULL(Num_CookingFor, 1) AS 'num_cook' FROM User WHERE GoogleAuth = " . $_SESSION['user_id'];
            $num_cook_res = mysqli_query($con, $num_cook_sql);
            $num_cook_row = mysqli_fetch_assoc($num_cook_res);
            $num_cook = $num_cook_row['num_cook'];

            if (empty($num_cook)) {$num_cook = 1;}
	
	    // Query to get ingredients and quantities according to number of people user is cooking for
            $get_ingredients = "SELECT DISTINCT CONCAT(IF(iq.quantity IS NULL, '', CONCAT(ROUND(((iq.quantity / r.servings) * " . $num_cook . "), 2), ' ')), IF(iq.metric IS NULL, '', CONCAT(iq.metric, ' ')), i.Name) AS ingredient
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
            
        </div>
    </div> 

</body>
</html>

