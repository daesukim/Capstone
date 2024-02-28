<?php

    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if(isset($data['recipe_id'])) {
        $recipeId = $data['recipe_id'];
        echo "<div class = 'pop_rec_name' id = 'pop_rec_name'>";
        $servername = "db.luddy.indiana.edu";
        $username = "i494f23_team25";
        $password = "my+sql=i494f23_team25";
        $dbname = "i494f23_team25";
        $con = mysqli_connect($servername, $username, $password, $dbname);

        if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error()); }
        $name_sql = "SELECT Name AS 'name' FROM Recipe WHERE RecipeID = $recipeId";
        $name_res = mysqli_query($con, $name_sql);
        $name = mysqli_fetch_assoc($name_res);
        
        echo "<h1>" . $name['name'] . "</h1>";
        echo "</div>
              <div class = 'img_ing'>
                <div class = 'pop_img'>";

        $recipe_image = "SELECT Url AS 'url' FROM Recipe AS r WHERE RecipeID = $recipeId";
        $url_res = mysqli_query($con, $recipe_image);
        $urlArray = mysqli_fetch_assoc($url_res);
        echo      "<img src='" . $urlArray['url'] . "' alt=''>";  
        echo      "<div class = 'recipe_info'>
                        <div class = 'type_allergen'>";
                        $dietary_sql = "SELECT DISTINCT dp.Preference AS 'preference'
                        FROM Recipe AS r
                        JOIN recipe_dietary AS rd ON r.RecipeID = rd.recipeID
                        JOIN Dietary_Preference AS dp ON rd.dietaryID = dp.PreferenceID
                        WHERE r.RecipeID = $recipeId";
            
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
                        WHERE r.RecipeID = $recipeId";
            
                        $allergy_res = mysqli_query($con, $allergy_sql);
                        if (mysqli_num_rows($allergy_res) > 0) {
                            $allergy = array();
                            while($row = mysqli_fetch_assoc($allergy_res)) {
                                array_push($allergy, $row['allergy']);
                            }
                        }

                        if (mysqli_num_rows($dietary_res) > 0) {
                            if (count($dietary) > 1) {
                                echo '<p>';
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
                                    echo '<p>' . $dietary[count($dietary) - 1] . '</p>';
                                }
                            }
                        }
                        if (mysqli_num_rows($allergy_res) > 0) {
                            echo '<p> Contains: ';
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

        echo           "</div>
                        <div class = 'cal_serv'>";
                        $cal_serv_sql = "SELECT r.servings AS 'servings', r.calories AS 'calories' FROM Recipe AS r WHERE r.RecipeID = $recipeId";
                        $cal_serv_res = mysqli_query($con, $cal_serv_sql);
                        $cal_serve = mysqli_fetch_assoc($cal_serv_res);
        echo           "<p>" . $cal_serve['calories'] . " calories </p>";
        echo           "<p>" . $cal_serve['servings'] . " servings </p>";
           
        echo           "</div>
                   </div>";
        echo   "</div>
                <div class = 'pop_ing'>";
                $get_ingredients = "SELECT CONCAT(IF(iq.quantity IS NULL, '', CONCAT(ROUND(iq.quantity, 2), ' ')), IF(iq.metric IS NULL, '', CONCAT(iq.metric, ' ')), i.Name) AS ingredient
                    FROM Ingredient AS i 
                    JOIN Ingredient_Quantity AS iq ON i.IngredientID = iq.IngredientID
                    JOIN Recipe AS r ON iq.recipeID = r.RecipeID
                    WHERE r.RecipeID = " . $recipeId;

                    $ingredients = mysqli_query($con, $get_ingredients);
                    if (mysqli_num_rows($ingredients) > 0) {
                        echo '<ul>'; 
                        while ($ingredient = mysqli_fetch_assoc($ingredients)) {
                            echo '<li class="ulItem">' . $ingredient['ingredient'] . '</li>';
                        }
                        echo '</ul>'; 
                    }
        echo   "</div>";
        echo  "</div>";
        echo   "<div class = 'meal_plan_add_button'>
                    <a href='add_page.php?menu_id=" . $recipeId . "'>
                        <button class = 'meal_plan_add'> Add to Meal Plan </button>
                    </a>
                </div>";
    } else {
        echo "No RecipeID received";
    }
?>