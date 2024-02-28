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

    // Delete recipe from meal plan
    if (isset($_REQUEST['confirm-delete'])) {
        $rID = mysqli_real_escape_string($con, $_POST['c-recipeID']);
        $mpID = mysqli_real_escape_string($con, $_POST['c-mpID']);
        $chosenDay = mysqli_real_escape_string($con, $_POST['c-recipeDay']);
        
        $delete = "DELETE FROM Meal_Plan_Event WHERE recipeID = " . $rID . " AND mealplanID = " . $mpID . " AND Chosen_Date = " . $chosenDay;
        $deleteRecipe = mysqli_query($con, $delete);
    }

    // Getting dates of selected meal plan or defaulting to most recent
    if (!empty($_POST['planDates'])) {
        $start_date = mysqli_real_escape_string($con, $_POST['planDates']);
        if (!empty($start_date)) {
        $start_date_conv = strtotime($start_date);
        $end_date_conv = strtotime("+7 day", $start_date_conv);
        $end_date = date("Y-m-d", $end_date_conv);
	}

    } else {
        $mp_query = "SELECT mp.startDate AS 'startDate', mp.endDate AS 'endDate'
        FROM Meal_Plan AS mp
        JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
        JOIN User AS u ON ump.userID = u.userID
        WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "' ORDER BY mp.endDate DESC";

        $meal_plan_res = mysqli_query($con, $mp_query);
        $meal_plan = mysqli_fetch_assoc($meal_plan_res);

        $start_date = $meal_plan["startDate"];
        $end_date = $meal_plan["endDate"];
        $start_date_conv = strtotime($start_date);
        $end_date_conv = strtotime($end_date);

    }
    
    echo '<div class="pageContent">
    <div id="paginationContainer" style="margin-top: 0; padding-top: 0;"></div>';
    $count_days = 0;
    if (empty($start_date_conv)) {$day_count = 0;} else {$day_count = date('w', $start_date_conv);}
    echo '<input type="hidden" value="' . $day_count . '" id="startDateNum">';
    while ($count_days <= 6) {
        // Display meal plan
        $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
        if ($day_count > 6) { $day_count = 0; }

        echo '<article>
        <div class="recipeItems">';

        $meals = array("Breakfast", "Lunch", "Dinner");
        for ($meal = 1; $meal <= 3; $meal++) {
            
            $get_recipe = "WITH selected_mp AS (
                SELECT mp.PlanID AS mealPlanID
                FROM Meal_Plan AS mp
                JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
                JOIN User AS u ON ump.userID = u.userID
                WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "'
                AND mp.startDate = '" . $start_date . "'
            )
            SELECT r.RecipeID AS 'recipeID', r.Name AS 'recipeName', mpe.Chosen_Date AS 'chosenDate', r.Url AS 'recipeUrl',
                CASE WHEN r.meal_type = 1 THEN 'Breakfast' WHEN r.meal_type = 2 THEN 'Lunch' WHEN r.meal_type = 3 THEN 'Dinner' END AS 'mealType',
                smp.mealPlanID AS 'mpID'
            FROM Meal_Plan_Event AS mpe
            JOIN Recipe AS r ON mpe.recipeID = r.RecipeID
            JOIN Meal_Plan AS mp ON mpe.mealplanID = mp.PlanID
            JOIN selected_mp AS smp ON smp.mealPlanID = mp.PlanID
            JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
            JOIN User AS u ON ump.userID = u.userID
            WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "'
            AND mpe.mealplanID = smp.mealPlanID
            AND mpe.Chosen_Date = " . $day_count . " AND r.meal_type = " . $meal;

            $recipe_res = mysqli_query($con, $get_recipe);

            if (mysqli_num_rows($recipe_res) > 0) {
                $recipe = mysqli_fetch_assoc($recipe_res);

                echo '<div class="dailyItem">
                    <div class="typeDelete">
                        <h3 class="day-h3">' . $recipe["mealType"] . '</h3>
                        <form name="deleteForm" id="deleteForm" action="" method="post">
                            <input type="hidden" name="recipeName" value="' . $recipe["recipeName"] . '">
                            <input type="hidden" name="recipeID" value="' . $recipe["recipeID"] . '">
                            <input type="hidden" name="mealType" value="' . $recipe["mealType"] . '">
                            <input type="hidden" name="chosenDate" value="' . $recipe["chosenDate"] . '">
                            <input type="hidden" name="mpID" value="' . $recipe["mpID"] . '">
                            <button type="submit" id="deleteRecipe" class="deleteButton">
                                <span class="material-symbols-outlined trash">delete</span>
                            </button>
                        </form>
                    </div>    
                    <form name="individualRecipe' . $recipe["recipeID"] . '" id="individualRecipe' . $recipe["recipeID"] . '" action="individual_recipe.php" method="post">
                        <input type="hidden" name="recipeToView" value="' . $recipe["recipeID"] . '">
                    </form>
                    <div class="recipeItem hvr-grow" onclick="submitForm(' . $recipe["recipeID"] . ')">
                        <img class="dailyImg" src="' . $recipe["recipeUrl"] . '" alt="' . $recipe["recipeName"] . '">
                        <p class="dailyDish">' . $recipe["recipeName"] . ' </p>
                    </div>
                </div>';
            } else {
                echo '<div class="emptyItem">
                    <h3 class="day-h3">' . $meals[$meal - 1] . '</h3>
                    <div class="emptyDiv">
                        <div class="emptyD"> <a href="view_recipe.php"> <span class="material-symbols-outlined dailyPlus hvr-grow">add_box</span> </a> </div>
                    </div>
                </div>';
            }

        }

        echo '</div>
        </article>';
        $count_days = $count_days + 1;
        $day_count = $day_count + 1;
    }

    echo '</div>';

    echo '<script>
        function setDaily() {
            document.getElementById("myCheckbox").checked = false;
        }
    </script>';

    // Popup to confirm deletion
    if(!empty($_POST['recipeID'])) {
        $id = mysqli_real_escape_string($con, $_POST['recipeID']);
        $name = mysqli_real_escape_string($con, $_POST['recipeName']);
        $type = mysqli_real_escape_string($con, $_POST['mealType']);
        $day = mysqli_real_escape_string($con, $_POST['chosenDate']);
        $mpID = mysqli_real_escape_string($con, $_POST['mpID']);

        $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

        echo '<div class="dailyPopup" id="dailyPopup" onload="setDaily()">
        <div class="dailyPopup-inner">
            <span class="logo material-symbols-outlined">grocery</span>
            <p>Are you sure you want to delete ' . $name . ' from ' . $days_of_week[$day] . '\'s ' . $type . '?</p>
            <br>
            <form name="deleteRecipe" id="deleteRecipe" action="" method="post">
                <input type="hidden" name="c-recipeID" value="' . $id . '">
                <input type="hidden" name="c-recipeType" value="' . $type . '">
                <input type="hidden" name="c-recipeDay" value="' . $day . '">
                <input type="hidden" name="c-mpID" value="' . $mpID . '">
                <div class="delete-buttons">
                    <input class="button hvr-grow" type="submit" name="confirm-delete" value="Yes">
                    <button type="button" class="button hvr-grow" id="closeDelete">No</button>
                </div>
            </form>
        </div>
        </div>';

        echo '<script> dailyPopup.classList.add("open"); </script>';
        echo '<script>
            document.getElementById("closeDelete").addEventListener("click", () => {
                dailyPopup.classList.add("close");
            });
        </script>';
    }
?>
