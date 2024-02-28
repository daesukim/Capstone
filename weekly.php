<?php
    // Sessions
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    // Establish connection
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
        $end_date_conv = strtotime("+6 day", $start_date_conv);
        $end_date = date("Y-m-d", $end_date_conv);
        echo '<div class="dateSwitch">
        <span class="material-symbols-outlined arrow" onclick="prevPlan();">arrow_back_ios</span>
	<h1 class="dates">' . date("M d", $start_date_conv) . ' - ' . date("M d", $end_date_conv) . '</h1>
	<span class="material-symbols-outlined arrow" onclick="nextPlan();">arrow_forward_ios</span>
        </div>';
	}    
} else {
        $mp_query = "SELECT mp.startDate AS 'startDate', mp.endDate AS 'endDate'
        FROM Meal_Plan AS mp
        JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
        JOIN User AS u ON ump.userID = u.userID
        WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "' ORDER BY mp.endDate DESC";

        $meal_plan_res = mysqli_query($con, $mp_query);
        $meal_plan = mysqli_fetch_assoc($meal_plan_res);

        if (!empty($meal_plan)) {
        $start_date = $meal_plan["startDate"];
        $start_date_conv = strtotime($start_date);
        $end_date_conv = strtotime("+6 day", $start_date_conv);

        echo '<div class="dateSwitch">
        <span class="material-symbols-outlined arrow" onclick="prevPlan();">arrow_back_ios</span>
	<h1 class="dates">' . date("M d", $start_date_conv) . ' - ' . date("M d", $end_date_conv) . '</h1>
	<span class="material-symbols-outlined arrow" onclick="nextPlan();">arrow_forward_ios</span>
        </div>';
	}   
 }

    // Javascript for previous and next meal plan
    echo '<script>
        function prevPlan() {';
            $getPrev = "WITH userPlans AS (
                SELECT mp.PlanID AS PlanID, mp.startDate AS startDate
                FROM user_mealplan AS ump
                JOIN User AS u ON ump.userID = u.UserID
                JOIN Meal_Plan AS mp ON ump.mealPlanID = mp.PlanID
                WHERE u.GoogleAuth= '" . $_SESSION['user_id'] . "'
                ORDER BY mp.startDate
            )
            SELECT startDate AS 'startDate'
            FROM userPlans 
            WHERE startDate = (SELECT MAX(startDate) FROM userPlans WHERE startDate < '" . $start_date . "')";

            $run_getPrev = mysqli_query($con, $getPrev);
            $getPrev_res = mysqli_fetch_assoc($run_getPrev);
            $startDate = $getPrev_res['startDate'];

            echo "let newStart = '" . $startDate . "';\n
            document.getElementById('planDates').value = newStart;\n
            document.getElementById('chosenPlan').submit();";
    echo '}

        function nextPlan() {';
            $getNext = "WITH userPlans AS (
                SELECT mp.PlanID AS PlanID, mp.startDate AS startDate
                FROM user_mealplan AS ump
                JOIN User AS u ON ump.userID = u.UserID
                JOIN Meal_Plan AS mp ON ump.mealPlanID = mp.PlanID
                WHERE u.GoogleAuth= '" . $_SESSION['user_id'] . "'
                ORDER BY mp.startDate
            )
            SELECT startDate
            FROM userPlans 
            WHERE startDate = (SELECT MIN(startDate) FROM userPlans WHERE startDate > '" . $start_date . "')";

            $run_getNext = mysqli_query($con, $getNext);
            $getNext_res = mysqli_fetch_assoc($run_getNext);
            $startDate = $getNext_res['startDate'];

            echo "let newStart = '" . $startDate . "';\n
            document.getElementById('planDates').value = newStart;\n
            document.getElementById('chosenPlan').submit();";
    echo '}
    </script>';

    // Display meal plan
    $days_of_week = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

    echo '<div class="days">';
    $count_days = 0;
    if (empty($start_date_conv)) {$day_count = 0;} else {$day_count = date('w', $start_date_conv);}
    // for ($day = 0; $day <= 6; $day++) {
    while ($count_days <= 6) {
        if ($day_count > 6) { $day_count = 0; }
        echo '<div class="day">
        <h3>' . $days_of_week[$day_count] . '</h3>
        <div class="recipes">';

        $meals = array("Breakfast", "Lunch", "Dinner");
        for ($meal = 1; $meal <= 3; $meal++) {

            $get_recipes = "WITH selected_mp AS (
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

            $recipes_res = mysqli_query($con, $get_recipes);
            
            if (mysqli_num_rows($recipes_res) > 0) {
                $recipe = mysqli_fetch_assoc($recipes_res);

                echo '<div class="recipe">
                    <div class="typeDelete">
                        <p class="mealType">' . $recipe["mealType"] . '</p>
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
                        <div class="recipeDiv hvr-grow" id="recipeDiv" onclick="submitForm(' . $recipe["recipeID"] . ')">
                            <img class="recipeImg" src="' . $recipe["recipeUrl"] . '" alt="' . $recipe["recipeName"] . '">
                            <p class="dishName">' . $recipe["recipeName"] . '</p>
                        </div>
                </div>';
            } else {
                echo '<div class="recipe">
                        <p class="mealType">' . $meals[$meal - 1] . '</p>
                    <div class="emptyW"> <a href="view_recipe.php"> <span class="material-symbols-outlined plus hvr-grow">add_box</span> </a> </div>
                </div>';
            }
        }

        echo '</div>
        </div>';
        $count_days = $count_days + 1;
        $day_count = $day_count + 1;
    }

    echo '</div>';

    // Popup to confirm deletion
    if(!empty($_POST['recipeID'])) {
        $id = mysqli_real_escape_string($con, $_POST['recipeID']);
        $name = mysqli_real_escape_string($con, $_POST['recipeName']);
        $type = mysqli_real_escape_string($con, $_POST['mealType']);
        $day = mysqli_real_escape_string($con, $_POST['chosenDate']);
        $mpID = mysqli_real_escape_string($con, $_POST['mpID']);

        echo '<div class="dPopup" id="dPopup">
        <div class="dPopup-inner">
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

        echo '<script> dPopup.classList.add("open"); </script>';
        echo '<script>
            document.getElementById("closeDelete").addEventListener("click", () => {
                dPopup.classList.add("close");
            });
        </script>';
    }

?>
