<?php 
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
?>
<html>
<head>
<title>
    Calorie Conscious Meal Plan
</title>

<!-- Style Sheet -->
<link rel="stylesheet" href="css/calorie.css">
<!-- Nav Bar Google Icon -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            <li><a href="landing.php">Home</a></li>
            <li><a href="view_recipe.php">View Recipes</a></li>
            <li><a href="recipe.php">Create Recipe</a></li>
            <li><a href="meal_plan.php">My Meal Plan</a></li>
            <li><a href="calorie.php">Calorie Conscious Meal Plan</a></li>
            <li><a href="profile.php">Profile Settings</a></li>
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

<!-- Javascript if add to plan is clicked without start date -->
<script>
    function addError() {
        alert("You must select a start date first.");
    }
</script>

<!-- Javascript to submit form -->
<script>
    function submitForm(recipeID) {
        document.getElementById("individualRecipe" + recipeID).submit();
    }
</script>

<?php
    // Establish connection
    $servername = "db.luddy.indiana.edu";
    $username = "i494f23_team25";
    $password = "my+sql=i494f23_team25";
    $dbname = "i494f23_team25";
    $con = mysqli_connect($servername, $username, $password, $dbname);

    if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error); }

    // Get info for calorie calculation
    $sql = "SELECT u.Weight AS 'weight', u.Height AS 'height', u.Gender AS 'gender', al.Level AS 'activity level', u.Age AS 'age'
    FROM User AS u
    JOIN Activity_level AS al ON u.activity_level = al.LevelID
    WHERE u.GoogleAuth = " . $_SESSION['user_id'];

    $sql_res = mysqli_query($con, $sql);
    $calorie = mysqli_fetch_assoc($sql_res);

    $weight = $calorie['weight'];
    $height = $calorie['height'];
    $gender = $calorie['gender'];
    $activity_level = $calorie['activity level'];
    $age = $calorie['age'];

    // Checking that user has necessary information in profile
    if (empty($weight) or empty($height) or empty($gender) or empty($activity_level) or empty($age)) {
        echo '<div class="dPopup" id="dPopup3">
        <div class="dPopup-inner">
            <br>
            <span class="logo material-symbols-outlined">grocery</span>
            <h4>WARNING</h4>
            <p>Please set your height, weight, gender, activity level, and age in profile settings to access this feature.</p>
            <br>
            <a href="profile.php"><button type="button" class="button hvr-grow">Settings</button></a>
            <br><br>
        </div>
        </div>';

        echo '<script> dPopup3.classList.add("open"); </script>';
    } else {

        // Do calorie calculation
        $kg_weight = $weight * 0.453592;
        $cm_height = $height * 2.54;

        if ($gender == 'M') {
            $bmr = 10 * $kg_weight + 6.25 * $cm_height - 5 * $age + 5;
        } else {
            $bmr = 10 * $kg_weight + 6.25 * $cm_height - 5 * $age - 161;
        }

        switch ($activity_level) {
            case "Sedentary":
                $cal_intake = $bmr * 1.2;
                break;
            case "Lightly Active":
                $cal_intake = $bmr * 1.375;
                break;
            case "Moderately Active":
                $cal_intake = $bmr * 1.55;
                break;
            case "Very Active":
                $cal_intake = $bmr * 1.725;
                break;
            case "Extra Active":
                $cal_intake = $bmr * 1.9;
                break;
        }

        $cal_intake = ceil($cal_intake);

        // Get number of people user is cooking for
        $num_cook_sql = "SELECT IFNULL(Num_CookingFor, 1) AS 'num_cook' FROM User WHERE GoogleAuth = " . $_SESSION['user_id'];
        $num_cook_res = mysqli_query($con, $num_cook_sql);
        $num_cook = mysqli_fetch_assoc($num_cook_res);

        $note = "";
        // If user is cooking for more than 1 person, adjust cal_intake
        if ($num_cook['num_cook'] > 1) {
            $cal_intake = $cal_intake * $num_cook['num_cook'];

            $note = '<p class="italic">Warning: You are cooking for ' . $num_cook["num_cook"] . ' people but this meal plan is only for one person.</p>';
        }

        // Get calories and IDs for all recipes according to how many people user is cooking for
        $breakfast_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories', r.RecipeID AS 'recipeID'
                                FROM Recipe AS r
                                WHERE r.meal_type = 1";
        $lunch_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories', r.RecipeID AS 'recipeID'
                            FROM Recipe AS r
                            WHERE r.meal_type = 2";
        $dinner_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories', r.RecipeID AS 'recipeID'
                            FROM Recipe AS r
                            WHERE r.meal_type = 3";
        
        $breakfast_res = mysqli_query($con, $breakfast_sql);
        $lunch_res = mysqli_query($con, $lunch_sql);
        $dinner_res = mysqli_query($con, $dinner_sql);

        $breakfast = mysqli_fetch_all($breakfast_res);
        $lunch = mysqli_fetch_all($lunch_res);
        $dinner = mysqli_fetch_all($dinner_res);

        // Display meal plan
        if (isset($_REQUEST['chooseDate'])) {
            $setStart = mysqli_real_escape_string($con, $_POST['startDate']);

            $user_plans_sql = "SELECT mp.startDate AS 'startDate'
            FROM user_mealplan AS ump
            JOIN User AS u ON ump.userID = u.UserID
            JOIN Meal_Plan AS mp ON ump.mealPlanID = mp.PlanID
            WHERE u.GoogleAuth= '" . $_SESSION['user_id'] . "'";

            $run_user_plans = mysqli_query($con, $user_plans_sql);

            $userPlans = array();
            while ($row = mysqli_fetch_assoc($run_user_plans)) {
                array_push($userPlans, $row['startDate']);
            }
            
            if (in_array($setStart, $userPlans)) {
                echo '<script> alert("This meal plan already exists. Please pick a new start date."); </script>';
                unset($_SESSION['start-date']);
            } else {
                $_SESSION['start-date'] = $setStart;
            }
        }

        if (isset($_SESSION['start-date'])) {
            $start_date = $_SESSION['start-date'];

            $start_date_conv = strtotime($start_date);
            $start_date_num = date('w', $start_date_conv);

            $buttonType = "submit";
            $buttonGrow = "button hvr-grow";
            $addError = '';
        } else { 
            $start_date_num = 0; 
            $buttonType = "button";
            $buttonGrow = "disabled";
            $addError = 'onclick="addError();"';
            $start_date = '';
        }

        // Initialize array to store unique identifiers of elements where remove buttons should be
        if(!isset($_SESSION['remove_buttons'])) {
            $_SESSION['remove_buttons'] = array();
        }

        // Initialize array for recipes where keep button has been clicked
        $kept_recipes = array();
        for ($i=0; $i<7; $i++) {
            $empty = array(0, 0, 0);
            array_push($kept_recipes, $empty);
        }

        // If a keep button has been clicked, add that to kept_recipes and remove_buttons
        if (!empty($_POST['keep-id'])) {
            $id = mysqli_real_escape_string($con, $_POST['keep-id']);
            $day = mysqli_real_escape_string($con, $_POST['keep-day']);
            $type_word = mysqli_real_escape_string($con, $_POST['keep-type']);
            $unique = mysqli_real_escape_string($con, $_POST['keep-unique']);

            switch ($type_word) {
                case "Breakfast":
                    $type = 0;
                    break;
                case "Lunch":
                    $type = 1;
                    break;
                case "Dinner":
                    $type = 2;
                    break;
            }

            if (!isset($_SESSION['kept_recipes'])) {
                $kept_recipes[$day][$type] = $id;
                $_SESSION['kept_recipes'] = $kept_recipes;
            } else {
                $_SESSION['kept_recipes'][$day][$type] = $id;
            } 

            if (!in_array($unique, $_SESSION['remove_buttons'])) {
                array_push($_SESSION['remove_buttons'], $unique);
            }

        }

        // Remove from remove_buttons and kept_recipes if remove button is clicked
        if (!empty($_POST['removeRecipe'])) {
            $toRemove = mysqli_real_escape_string($con, $_POST['removeRecipe']);
            $removeUnique = mysqli_real_escape_string($con, $_POST['removeRecipeUnique']);

            $indexes = explode(',', $toRemove);

            $index_1 = $indexes[0];
            $index_2 = $indexes[1];

            $index_1 = (int)$index_1;
            $index_2 = (int)$index_2 - 1;

            $_SESSION['kept_recipes'][$index_1][$index_2] = 0;

            if (($key = array_search($removeUnique, $_SESSION['remove_buttons'])) !== false) {
                unset($_SESSION['remove_buttons'][$key]);
            }
        }

        // Get weekly meal plan based on user's suggested daily calorie intake
        $week_recipes = array();
        while (count($week_recipes) < 7) {
            $total_cal = 0;
            // Allowing total calories in a day to be within 100 calories of suggested calorie intake
            while (!($total_cal <= $cal_intake + 100 and $total_cal >= $cal_intake - 100)) {
                // If recipe is kept, then get the saved recipe, If not get a random recipe
                if ($_SESSION['kept_recipes'][count($week_recipes)][0] != 0) {
                    $b_id = $_SESSION['kept_recipes'][count($week_recipes)][0];

                    $cal_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories'
                    FROM Recipe AS r
                    WHERE r.RecipeID = " . $b_id;

                    $run_cal_sql = mysqli_query($con, $cal_sql);
                    $cal_sql_res = mysqli_fetch_assoc($run_cal_sql);

                    $b_cal = $cal_sql_res['calories'];
                } else {
                    $rand_b = $breakfast[array_rand($breakfast)];
                    $b_cal = $rand_b[0];
                    $b_id = $rand_b[1];
                }

                if ($_SESSION['kept_recipes'][count($week_recipes)][1] != 0) {
                    $l_id = $_SESSION['kept_recipes'][count($week_recipes)][1];

                    $cal_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories'
                    FROM Recipe AS r
                    WHERE r.RecipeID = " . $l_id;

                    $run_cal_sql = mysqli_query($con, $cal_sql);
                    $cal_sql_res = mysqli_fetch_assoc($run_cal_sql);

                    $l_cal = $cal_sql_res['calories'];
                } else {
                    $rand_l = $lunch[array_rand($lunch)];
                    $l_cal = $rand_l[0];
                    $l_id = $rand_l[1];
                }

                if ($_SESSION['kept_recipes'][count($week_recipes)][2] != 0) {
                    $d_id = $_SESSION['kept_recipes'][count($week_recipes)][2];

                    $cal_sql = "SELECT CEILING((r.calories / r.servings) * " . $num_cook['num_cook'] . ") AS 'calories'
                    FROM Recipe AS r
                    WHERE r.RecipeID = " . $d_id;

                    $run_cal_sql = mysqli_query($con, $cal_sql);
                    $cal_sql_res = mysqli_fetch_assoc($run_cal_sql);

                    $d_cal = $cal_sql_res['calories'];
                } else {
                    $rand_d = $dinner[array_rand($dinner)];
                    $d_cal = $rand_d[0];
                    $d_id = $rand_d[1];
                }
                $total_cal = $b_cal + $l_cal + $d_cal;
            }
            $day_recipe = array();
            array_push($day_recipe, $b_id);
            array_push($day_recipe, $l_id);
            array_push($day_recipe, $d_id);

            array_push($week_recipes, $day_recipe);
        }

        echo '<div class="topButtons">
        <form name="chooseDate" action="" method="post" id="chooseDate">
            <div class="chooseDate">
                <p class="startText">Start Date: </p>
                <input type="date" class="datePicker" name="startDate" min="' . date("Y-m-d") . '" value="' . $start_date . '">
                <button class="button dateSubmit hvr-grow" name="chooseDate" type="submit" onclick="dateCheck();">Submit</button>
            </div>
        </form>
        <form name="add-to-plan" id="add-to-plan" action="" method="post">
            <input type="hidden" name="form-startDate" value="' . $start_date . '">';

            $weekDays = array('sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat');
            $count = 0;
            foreach($week_recipes as $day) {
                foreach($day as $meal) {
                    echo '<input type="hidden" name="' . $weekDays[$count] . '[]" value="'. $meal. '">';
                }
                $count = $count + 1;
            }

            echo '<div class="addButton"><button class="' . $buttonGrow . '" type="' . $buttonType . '" name="add-to-plan" ' . $disabled . ' title="Select start date to add to meal plan" ' . $addError . '>Add to Meal Plan</button></div>
        </form>
        </div>
        <div class="topContent">
            <h1 class="calIntake">Daily Calorie Intake: ' . $cal_intake . '</h1>
            ' . $note . '
            <button class="button hvr-grow" onclick="window.location.reload();"><div class="reg">Regenerate<span class="material-symbols-outlined">autorenew</span></div></button>
        </div>';
        echo '<br>';

        if (isset($_REQUEST['add-to-plan'])) {
            $start_date = mysqli_real_escape_string($con, $_POST['form-startDate']);
            $start_date_conv = strtotime($start_date);
            $end_date_conv = strtotime("+7 day", $start_date_conv);
            $end_date_display = strtotime("+6 day", $start_date_conv);
            $end_date = date("Y-m-d", $end_date_conv);

            $add_recipes = array();
            array_push($add_recipes, $_POST['sun']);
            array_push($add_recipes, $_POST['mon']);
            array_push($add_recipes, $_POST['tues']);
            array_push($add_recipes, $_POST['wed']);
            array_push($add_recipes, $_POST['thurs']);
            array_push($add_recipes, $_POST['fri']);
            array_push($add_recipes, $_POST['sat']);

            $insert_mp = "INSERT INTO Meal_Plan (startDate, endDATE) VALUES ('$start_date', '$end_date')";
            $run_insert_mp = mysqli_query($con, $insert_mp);

            $mp_id_sql = "SELECT PlanID AS 'planID' FROM Meal_Plan ORDER BY PlanID DESC LIMIT 1";
            $run_mp_id = mysqli_query($con, $mp_id_sql);
            $mp_id = mysqli_fetch_assoc($run_mp_id);

            $user_id_sql = "SELECT UserID AS 'userID' FROM User WHERE GoogleAuth = " . $_SESSION['user_id'];
            $run_user_id = mysqli_query($con, $user_id_sql);
            $user_id_res = mysqli_fetch_assoc($run_user_id);
            $user_id = $user_id_res['userID'];

            $plan_id = $mp_id['planID'];
            
            $insert_ump = "INSERT INTO user_mealplan (userID, mealPlanID) VALUES ('$user_id', '$plan_id')";
            $run_insert_ump = mysqli_query($con, $insert_ump);

            $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

            $start_date_num = date('w', $start_date_conv);

            $count = 0;
            while ($count < 7) {
                if ($start_date_num > 6) { $start_date_num = 0;}
                $day = $add_recipes[$start_date_num];
                foreach ($day as $recipe_id) {
                    $insert_mpe = "INSERT INTO Meal_Plan_Event (recipeID, mealplanID, Chosen_Date) VALUES ('$recipe_id', '$plan_id', '$start_date_num')";
                    $run_insert_mpe = mysqli_query($con, $insert_mpe);
                }
                $start_date_num = $start_date_num + 1;
                $count = $count + 1;
            }

            echo '<div class="dPopup" id="dPopup">
            <div class="dPopup-inner">
                <br>
                <span class="logo material-symbols-outlined">grocery</span>
                <p>Meal plan for ' . date("M d", $start_date_conv) . ' to ' . date("M d", $end_date_display) . ' has been added!</p>
                <br>
                <div class="popupButtons">
                    <form name="viewPlan" action="meal_plan.php" method="post" id="viewPlan">
                        <input type="hidden" id="calStart" name="calStart" value="' . $start_date . '">
                        <a href="meal_plan.php"><button type="submit" class="button hvr-grow" name="calPlanSubmit" id="calPlanSubmit">View Plan</button></a>
                    </form>
                    <button type="button" class="closeButton hvr-grow" id="closeDelete">Close</button>
                </div>
                <br>
            </div>
            </div>';

            echo '<script> dPopup.classList.add("open"); </script>';
            echo '<script>
                document.getElementById("closeDelete").addEventListener("click", () => {
                    dPopup.classList.add("close");
                });
            </script>';
        }

        echo '<div class="days">';
            $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
            $meals = array("Breakfast", "Lunch", "Dinner");
            $day_count = $start_date_num;
            $count = 0;
            while ($count < 7) {
                if ($day_count > 6) { $day_count = 0; }
                echo '<div class="day">
                <h3>' . $days_of_week[$day_count] . '</h3>
                <div class="recipes">';
                
                $day = $week_recipes[$day_count];
                foreach ($day as $recipe_id) {
                    $get_recipe = "SELECT RecipeID AS 'recipeID', Name AS 'recipeName', Url AS 'recipeUrl',
                    CASE WHEN meal_type = 1 THEN 'Breakfast' WHEN meal_type = 2 THEN 'Lunch' WHEN meal_type = 3 THEN 'Dinner' END AS 'mealType'
                    FROM Recipe
                    WHERE RecipeID = " . $recipe_id;

                    $recipes_res = mysqli_query($con, $get_recipe);
                    $recipe = mysqli_fetch_assoc($recipes_res);
                    $meal_type_sql = $recipe["mealType"];
                    switch($meal_type_sql) {
                        case "Breakfast":
                            $meal_type = 1;
                            break;
                        case "Lunch":
                            $meal_type = 2;
                            break;
                        case "Dinner":
                            $meal_type = 3;
                            break;
                    }
                    $unique = $day_count . $meal_type . $recipe["recipeID"];

                    echo '<div class="recipe">
                        <div class="keepDiv">
                            <p class="mealType">' . $recipe["mealType"] . '</p>
                            <form name="keepForm' . $recipe["recipeID"] . '" action="" method="post" id="keepForm' . $recipe["recipeID"] . '">';

                                if (in_array($unique, $_SESSION['remove_buttons'])) {
                                    $value = '<span class="material-symbols-outlined">close</span>';
                                    $class = 'remove hvr-grow';
                                    $elementId = 'removeSubmit';
                                    echo '<input type="hidden" name="removeRecipe" id="removeRecipe" value="' . $day_count . ',' . $meal_type . '">
                                    <input type="hidden" name="removeRecipeUnique" id="removeRecipe" value="' . $unique . '">';
                                } else {
                                    $value = 'Keep';
                                    $class = 'button keep hvr-grow';
                                    $elementId = 'keepSubmit';
                                    echo '<input type="hidden" name="keep-id" value="' . $recipe["recipeID"] . '">
                                    <input type="hidden" name="keep-day" value="' . $day_count . '">
                                    <input type="hidden" name="keep-type" value="' . $recipe["mealType"] . '">
                                    <input type="hidden" name="keep-unique" value="' . $unique . '">';
                                }

                                if (!isset($_SESSION['start-date'])) {
                                    $class = "disabledK";
                                    $hide = 'style="display: none;"';
                                    $buttonTypeK = 'button';
                                } else {
                                     $hide = '';
                                     $buttonTypeK = 'submit';
                                }

                                echo '<button type="' . $buttonTypeK . '" class="' . $class . '" id="' . $elementId . $unique . '" ' . $hide . '>' . $value . '</button>
                            </form>
                        </div>
                        <form name="individualRecipe' . $recipe["recipeID"] . '" id="individualRecipe' . $recipe["recipeID"] . '" action="individual_recipe.php" method="post">
                            <input type="hidden" name="recipeToView" value="' . $recipe["recipeID"] . '">
                        </form>
                            <div class="recipeDiv hvr-grow" id="recipeDiv" onclick="submitForm(' . $recipe["recipeID"] . ')">
                                <img class="recipeImg" src="' . $recipe["recipeUrl"] . '" alt="' . $recipe["recipeName"] . '">
                                <p class="dishName">' . $recipe["recipeName"] . '</p>
                            </div>
                    </div>';
                    }
                    echo '</div>
                </div>';
                $day_count = $day_count + 1;
                $count = $count + 1;
            }
    }
?>

</body>
</html>
