<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$_mealplanID = $_POST["meal_plan_id"];
$_menuID = $_GET["menuID"];

echo "mealplanID is " . $_mealplanID;
echo "menuID is " . $_menuID;


$conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
if (!$conn) { die("Connection failed: " . mysqli_connect_error());}

// getting meal_type of the chosen recipe
$sql = "SELECT meal_type
        FROM Recipe
        WHERE RecipeID = " . $_menuID;

$result = mysqli_query($conn, $sql);

if ($_mealplanID == "none"){
    $_SESSION['error_message'] = "Meal plan is not selected";
    header("Location: add_page.php?menu_id=" . $_menuID);
    exit();
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $meal_type = $row['meal_type'];
    echo "meal_type is " . $meal_type;
    
    // checking if the provided recipe can be stored in the meal_plan
    if (isset($_POST["days"]) && is_array($_POST["days"])) {
        foreach ($_POST["days"] as $dayID) {
            $sql2 = "SELECT count(*) AS count
            FROM Meal_Plan_Event as mpe
            JOIN Recipe as r ON r.RecipeID = mpe.recipeID
            JOIN Meal_Plan as mp ON mpe.mealplanID = mp.PlanID
            WHERE mp.PlanID = " . $_mealplanID . " AND r.meal_type = " . $meal_type . " AND mpe.Chosen_Date = " . $dayID;

            $result2 = mysqli_query($conn, $sql2);

            if ($result2){
                $row = mysqli_fetch_assoc($result2);
                $count = $row['count'];

                if ($count > 0) {
                    $_SESSION['error_message'] = "Cannot add recipe to meal plan. Recipe already exists for one or more selected days.";
                    header("Location: add_page.php?menu_id=" . $_menuID);
                    exit();
                }
                else{
                    continue;
                }
            }
        }
    }
    foreach ($_POST["days"] as $dayID) {
        $sql3 = "INSERT INTO Meal_Plan_Event (recipeID, mealplanID, Chosen_Date)
                 VALUES ($_menuID, $_mealplanID, $dayID)";
        $result3 = mysqli_query($conn, $sql3);
    }
    header("Location: view_recipe.php");
}
?>