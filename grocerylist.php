<?php

// Sessions
session_start();
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
 exit();
}


$host = 'db.luddy.indiana.edu'; // Host name
$username = 'i494f23_team25'; // Username
$password = 'my+sql=i494f23_team25'; // Password
$dbname = 'i494f23_team25'; // Database name

$con = mysqli_connect("db.luddy.indiana.edu", "i494f23_team25",
"my+sql=i494f23_team25", "i494f23_team25");
if (!$con) {
die("Failed to connect to MySQL: " .
mysqli_connect_error() );
} else {
echo " " ;
}

$start_date = mysqli_real_escape_string($con, $_POST['glStart']);


if (empty($start_date)) {
$query = "WITH most_recent_mp AS (
    SELECT mp.PlanID AS mealPlanID, mp.endDate
    FROM Meal_Plan AS mp
    JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
    JOIN User AS u ON ump.userID = u.userID
    WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "'
    ORDER BY mp.endDate DESC
    LIMIT 1)
SELECT 
I.Name AS Ingredient,
CONCAT(
    CEIL(SUM(COALESCE(IQ.quantity, 0) * COALESCE(U.Num_CookingFor, 1) / R.servings)),
    ' ',
    COALESCE(IQ.metric, '')
) AS TotalQuantity
FROM 
User U
JOIN user_mealplan UMP ON U.UserID = UMP.userID
JOIN Meal_Plan MP ON UMP.mealPlanID = MP.PlanID
JOIN Meal_Plan_Event MPE ON MP.PlanID = MPE.mealplanID
JOIN most_recent_mp AS mr ON mr.mealPlanID = MP.PlanID
JOIN Recipe R ON MPE.recipeID = R.RecipeID
JOIN Ingredient_Quantity IQ ON R.RecipeID = IQ.recipeID
JOIN Ingredient I ON IQ.IngredientID = I.IngredientID
WHERE 
MPE.mealplanID = mr.mealPlanID AND U.GoogleAuth = '" . $_SESSION['user_id'] . "'
GROUP BY 
I.Name, IQ.metric";
} else {
    $query = "SELECT
    I.Name AS Ingredient,
    CONCAT(
        CEIL(SUM(COALESCE(IQ.quantity, 0) * COALESCE(U.Num_CookingFor, 1) / R.servings)),
        ' ',
        COALESCE(IQ.metric, '')
    ) AS TotalQuantity
    FROM
    User U
    JOIN user_mealplan UMP ON U.UserID = UMP.userID
    JOIN Meal_Plan MP ON UMP.mealPlanID = MP.PlanID
    JOIN Meal_Plan_Event MPE ON MP.PlanID = MPE.mealplanID
    JOIN Recipe R ON MPE.recipeID = R.RecipeID
    JOIN Ingredient_Quantity IQ ON R.RecipeID = IQ.recipeID
    JOIN Ingredient I ON IQ.IngredientID = I.IngredientID
    WHERE U.GoogleAuth = '" . $_SESSION['user_id'] . "'
    AND MP.startDate = '" . $start_date . "'
    GROUP BY
    I.Name, IQ.metric";}


$result = mysqli_query($con, $query);

$listText = "";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/grocery_list_styles.css">
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

<span class="material-symbols-outlined hvr-grow backButton" onclick="history.go(-1);">arrow_back </span>

<!-- Top Buttons -->
<div class="topContent">
    <div class="topButtons">
        <div class="calGrocery">
            <button id="copyButton">Copy to Clipboard</button>
        </div>


<h1> Your Grocery List </h1>

<table>    
    <tr>
        <th>Ingredient</th>
        <th>Total Quantity</th>
    </tr> 


<?php


while ($row = mysqli_fetch_assoc($result)){
    $ingredient = $row['Ingredient'];
    $total_quantity = $row['TotalQuantity'];
    $listText .= $ingredient . ": " . $total_quantity . "\n"; // Append each item

    echo '<tr>';
            echo '<td>'. $ingredient .'</td>';
            echo '<td>'. $total_quantity .'</td>';
    echo '</tr>';
          
}

?> 

</table>

<div id="textList" style ="display:none;"><?php echo $listText?></div>


<script>
document.getElementById('copyButton').addEventListener('click', async function() {
    var text = document.getElementById('textList').textContent;
    console.log("#textList content:", text);
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            alert('Grocery list copied to clipboard!');
        } catch (err) {
            console.error('Could not copy text: ', err);
            alert('Failed to copy the grocery list.');
        }
    } else {
        // Fallback for older browsers
        var textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        console.log(text);

        try {
            var successful = document.execCommand('copy');
            var msg = successful ? 'successful' : 'unsuccessful';
            console.log('Fallback: Copying text command was ' + msg);
            alert('Grocery list copied to clipboard!');
        } catch (err) {
            console.error('Fallback: Could not copy text', err);
            alert('Failed to copy the grocery list.');
        } finally {
            document.body.removeChild(textarea);
        }
    }
});

</script>
<?php

mysqli_close($con);

?>

</body>
</html>

