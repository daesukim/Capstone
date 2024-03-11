<html>
<head>
    <!-- Nav Bar Style Sheet -->
    <link rel="stylesheet" href="css/view_recipe.css">
    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- view recipe pagination javascript -->
    <script src="js/recipe.js"></script>

    <!-- plus icon on recipe -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
    <?php session_start(); ?>
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

    <!-- Page Content -->
    <div class = "main">
        <!-- Meal-type navigation -->
        <div class = "recipe_content">
            <div class = "filter">
                <div class="meal_type_nav">
                    <form class = "meal_type_form" onchange="meal_type_dropdown()">
                        <select id="mealSelect">
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="dinner">Dinner</option>
                        </select>
                    </form>
                    <script>
                        function meal_type_dropdown() {
                            var selectElement = document.getElementById("mealSelect");
                            var selectedValue = selectElement.value;

                            if (selectedValue === "breakfast") {
                                window.location.href = "view_recipe.php?category=breakfast";
                            } else if (selectedValue === "lunch") {
                                window.location.href = "view_recipe.php?category=lunch";
                            } else if (selectedValue === "dinner") {
                                window.location.href = "view_recipe.php?category=dinner";
                            }
                        }
                        document.addEventListener("DOMContentLoaded", function () {
                            var selectElement = document.getElementById("mealSelect");
                            var urlParams = new URLSearchParams(window.location.search);
                            if (urlParams.has('category') && urlParams.get('category') === 'breakfast') {
                                selectElement.value = "breakfast";
                            }
                            else if (urlParams.has('category') && urlParams.get('category') === 'lunch') {
                                selectElement.value = "lunch";
                            }
                            else if (urlParams.has('category') && urlParams.get('category') === 'dinner') {
                                selectElement.value = "dinner";
                            }
                            else{
                                selectElement.value = "breakfast";
                            }
                        });
                    </script>
                </div>
                <form class = "search_bar_container" action="get_searchbar_content.php?category=<?php echo $_GET['category']; ?>" method="POST">
                    <div class = "search_bar">
                        <div class = "search">
                            <input type = "text" id = "ingredient_search" name = "ingredient_search" placeholder="search ingredients"></input>
                        </div>
                        <div class = "search_icon">
                            <button type="submit" class = "search_button">
                                <i class="fa fa-search" style="color: #1f612d;"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <!-- start of the cuisine and order by dropdown form -->
                <form class = "drop_responsive" action="get_filtered_content.php?category=<?php echo $_GET['category']; ?>" method="POST">
                <div class = "cuisine_drop_container">
                    <select name="cusine_dropdown">
                        <option value = '0'> None </option>
                        <?php
                            $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                            if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                            $sql = "SELECT TypeID, TypeName FROM Cuisine_Type
                            ORDER BY TypeName";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id=$row['TypeID'];
                                $typeName=$row['TypeName'];
                                echo  "<option value='". $id. "'>" . $typeName. "</option>";
                            }
                        ?>
                    </select>
                </div>
                <?php
                $category = $_GET['category'];
                echo '<input type="hidden" name="category" value="' . $category . '">';
                ?>
                <div class = "order_by_drop_container">
                    <select name="order_dropdown">
                        <option value="none">None</option>
                        <option value="ASC">A - Z</option>
                        <option value="DESC">Z - A</option>
                    </select>
                </div>
                <div class = "filter_submit_container">
                    <input type="submit" value="Filter">
                </div>
                <a class = "go_to_plan" href = "meal_plan.php">Go To Plan</a>
                </form> <!-- end of form -->
            </div>
            
            <div class = "recipe_container">
                <div id="menu-list">
                    <!-- menus are here -->
                    <?php
                    if (isset($_GET['category'])) {
                        $category = $_GET['category'];
                        $searched_ingredient = $_POST['ingredient_search'];

                        switch ($category) {
                            case 'breakfast':
                                $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                                if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                                $sql = "SELECT r.RecipeID AS RecipeID, r.Name AS Name, r.url AS url
                                FROM Recipe AS r
                                JOIN Ingredient_Quantity AS ig ON r.RecipeID = ig.recipeID
                                JOIN Ingredient AS i ON ig.IngredientID = i.IngredientID
                                WHERE r.meal_type = 1 AND i.Name LIKE '%" . $searched_ingredient . "%'
                                GROUP BY RecipeID";
                                $result = mysqli_query($conn, $sql);
                                break;
                            case 'lunch':
                                $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                                if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                                $sql = "SELECT r.RecipeID AS RecipeID, r.Name AS Name, r.url AS url
                                FROM Recipe AS r
                                JOIN Ingredient_Quantity AS ig ON r.RecipeID = ig.recipeID
                                JOIN Ingredient AS i ON ig.IngredientID = i.IngredientID
                                WHERE r.meal_type = 2 AND i.Name LIKE '%" . $searched_ingredient . "%'
                                GROUP BY RecipeID";
                                $result = mysqli_query($conn, $sql);
                                break;
                            case 'dinner':
                                $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                                if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                                $sql = "SELECT r.RecipeID AS RecipeID, r.Name AS Name, r.url AS url
                                FROM Recipe AS r
                                JOIN Ingredient_Quantity AS ig ON r.RecipeID = ig.recipeID
                                JOIN Ingredient AS i ON ig.IngredientID = i.IngredientID
                                WHERE r.meal_type = 3 AND i.Name LIKE '%" . $searched_ingredient . "%'
                                GROUP BY RecipeID";
                                $result = mysqli_query($conn, $sql);
                                break;
                            default:
                                $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                                if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                                $sql = "SELECT r.RecipeID AS RecipeID, r.Name AS Name, r.url AS url
                                FROM Recipe AS r
                                JOIN Ingredient_Quantity AS ig ON r.RecipeID = ig.recipeID
                                JOIN Ingredient AS i ON ig.IngredientID = i.IngredientID
                                WHERE r.meal_type = 1 AND i.Name LIKE '%" . $searched_ingredient . "%'
                                GROUP BY RecipeID";
                                $result = mysqli_query($conn, $sql);
                                break;
                        }
                    } else {
                        $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
                        if (!$conn) { die("Connection failed: " . mysqli_connect_error());}
                        $sql = "SELECT r.RecipeID AS RecipeID, r.Name AS Name, r.url AS url
                        FROM Recipe AS r
                        JOIN Ingredient_Quantity AS ig ON r.RecipeID = ig.recipeID
                        JOIN Ingredient AS i ON ig.IngredientID = i.IngredientID
                        WHERE r.meal_type = 1 AND i.Name LIKE '%" . $searched_ingredient . "%'
                        GROUP BY RecipeID";
                        $result = mysqli_query($conn, $sql);
                    }
                    while ($row = mysqli_fetch_assoc($result)) {
                        $recipe_id=$row['RecipeID'];
                        $name=$row['Name'];
                        echo  "<div class='menu'>";
                        echo "<div class = 'menu_name pop_selector' id = '" . $recipe_id . "'><p>" . $name . "</p></div>";
                        echo "<div style='background-image: url(\"" . $row['url'] . "\");' class='menu-image-container'>";
                        echo "<div id = 'recipe-add-button' class = 'recipe-add-button'>";
                        echo '<a href="add_page.php?menu_id=' . urlencode($row['RecipeID']) . '">';
                        echo "<i class='fa fa-plus'></i>";
                        echo "</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            

            <!-- bottom pagination -->
            <div id="pagination-container">
                <button class = "page-button" id="prev-btn">&laquo;</button>
                <span id="page-info">1</span>
                <button class = "page-button" id="next-btn">&raquo;</button>
            </div>
        </div>
    </div>

    <div class = "hidden_section hidden">
        <div class = "rec_info">
        </div>
    </div>

    <script>
        var menus = document.querySelectorAll('.pop_selector');
        
        document.addEventListener('DOMContentLoaded', function() {
            for (var i = 0; i < menus.length; i++) {
                menus[i].addEventListener('click', function() {
                    var recipeId = this.id;
                    console.log("This is " + recipeId);
                    var content = document.querySelector('.hidden_section');
                    if (content) {
                        content.classList.remove('hidden');
                        content.classList.add('visible');
                        sendRecipeIdToPhp(recipeId);
                    } else {
                        console.error("Element with class 'hidden_section' not found.");
                    }
                });
            }
        });

        function sendRecipeIdToPhp(recipeId) {
            fetch('pop_recipe_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ recipe_id: recipeId }),
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                document.querySelector('.rec_info').innerHTML = data + "<div class = 'close' id = 'close'></div>";
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
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