<?php
// Assuming you have a database connection established

// Your database credentials
error_reporting(E_ALL);
ini_set('display_errors', '1');
$servername = "db.luddy.indiana.edu";
$username = "i494f23_team25";
$password = "my+sql=i494f23_team25";
$database = "i494f23_team25";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $recipeName = mysqli_real_escape_string($conn, $_POST["name"]);
    $cuisineType = (int)$_POST["cuisine_type"];  // Cast to integer
    $mealType = (int)$_POST["meal_type"];        // Cast to integer
    $calories = mysqli_real_escape_string($conn, $_POST["calories"]);
    $servings = mysqli_real_escape_string($conn, $_POST["serving"]);

    // Step 1: Check if Cuisine_Type with given TypeID exists
    $checkCuisineTypeQuery = "SELECT TypeID FROM Cuisine_Type WHERE TypeID = ?";
    $stmt = $conn->prepare($checkCuisineTypeQuery);
    $stmt->bind_param("i", $cuisineType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    
    
    

   
    if ($result->num_rows == 0) {
        // Cuisine type does not exist, insert into Cuisine_Type table
        $insertCuisineTypeQuery = "INSERT INTO Cuisine_Type (TypeID, TypeName) VALUES (?, ?)";
        $stmt = $conn->prepare($insertCuisineTypeQuery);
        $stmt->bind_param("i", $cuisineType);
        $stmt->execute();
    }
    /* Get the name of the file uploaded to Apache */
$filename = $_FILES['file']['name'];

/* Prepare to save the file upload to the upload folder */
$location = "image/".$filename;

/* Permanently save the file upload to the upload folder */
if ( move_uploaded_file($_FILES['file']['tmp_name'], $location) ) { 
  echo '<p>Your image upload was success</p>'; 
} else { 
  echo '<p>Try to upload another image</p>'; 
}

$fileUrl = 'https://cgi.luddy.indiana.edu/~team25/project/image/' . basename($_FILES["file"]["name"]);

    // Step 2: Insert into Recipe table
    $insertRecipeQuery = "INSERT INTO Recipe (cuisine_type, Name, servings, calories, meal_type, Url) 
    VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertRecipeQuery);
$stmt->bind_param("issiis", $cuisineType, $recipeName, $servings, $calories, $mealType, $fileUrl);


$successMessage = ""; // Initialize success message



if ($stmt->execute()) {
    // Get the last inserted recipe ID
    $recipeID = $stmt->insert_id;
        
    $ingredientNames = $_POST["ingredient_name"];
    $ingredientQuantities = $_POST["ingredient_quantity"];
    $ingredientMetrics = $_POST["ingredient_metric"];

    foreach ($ingredientNames as $key => $ingredientName) {
        // Step 3.1: Insert into Ingredient table
        $insertIngredientQuery = "INSERT INTO Ingredient (Name) VALUES (?)";
        $stmt = $conn->prepare($insertIngredientQuery);
        $stmt->bind_param("s", $ingredientName);
        $stmt->execute();


        // Get the last inserted ingredient ID
        $ingredientID = $stmt->insert_id;

        // Step 3.2: Insert into Ingredient_Quantity table
        $insertIngredientQuantityQuery = "INSERT INTO Ingredient_Quantity (recipeID, ingredientID, quantity, metric) 
                                          VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertIngredientQuantityQuery);
        $stmt->bind_param("iiis", $recipeID, $ingredientID, $ingredientQuantities[$key], $ingredientMetrics[$key]);
        $stmt->execute();
    }
    $insertRecipeStepsQuery = "INSERT INTO Recipe_Steps (RecipeID, Step, StepNumber) VALUES (?, ?, ?)";
    
    foreach ($_POST["recipe_steps"] as $key => $recipeStep) {
        $stepNumber = $_POST["step_numbers"][$key];
        $stmt = $conn->prepare($insertRecipeStepsQuery);
        $stmt->bind_param("isi", $recipeID, $recipeStep, $stepNumber);
        $stmt->execute();
    }
    if (isset($_POST["Allergy"])) {
        $allergyIDs = $_POST["Allergy"];
        foreach ($allergyIDs as $allergyID) {
            $insertRecipeAllergyQuery = "INSERT INTO recipe_allergy (recipeID, allergyID) VALUES (?, ?)";
            $stmtAllergy = $conn->prepare($insertRecipeAllergyQuery);
            
            if ($stmtAllergy) {
                $stmtAllergy->bind_param("ii", $recipeID, $allergyID);
                $stmtAllergy->execute();
            } else {
                echo "Error preparing statement for recipe_allergy: " . $conn->error;
            }
        }
    }
    
    // Insert into recipe_preference table
    if (isset($_POST["Preference"])) {
        $preferenceIDs = $_POST["Preference"];
        foreach ($preferenceIDs as $preferenceID) {
            $insertRecipePreferenceQuery = "INSERT INTO recipe_dietary (recipeID, dietaryID) VALUES (?, ?)";
            $stmtPreference = $conn->prepare($insertRecipePreferenceQuery);
            
            if ($stmtPreference) {
                $stmtPreference->bind_param("ii", $recipeID, $preferenceID);
                $stmtPreference->execute();
            } else {
                echo "Error preparing statement for recipe_preference: " . $conn->error;
            }
        }
    }


        // Display success message or redirect to a success page
        echo "Recipe inserted successfully!";
    } else {
        // Display an error message or handle the error accordingly
        echo "Error: " . $stmt->error;
    }
}

/*image insert*/





// Fetch cuisine types from the Cuisine_Type table
$sqlCuisine = "SELECT TypeID, TypeName FROM Cuisine_Type";
$resultCuisine = $conn->query($sqlCuisine);

// Fetch meal types from the Meal_Type table
$sqlMeal = "SELECT TypeID, Type FROM Meal_Type";
$resultMeal = $conn->query($sqlMeal);

$sqlMetrics = "SELECT DISTINCT metric FROM Ingredient_Quantity";
$resultMetrics = $conn->query($sqlMetrics);

$sqlPreference = "SELECT PreferenceID, Preference FROM Dietary_Preference";
$resultPreference = $conn->query($sqlPreference);

$sqlAllergy = "SELECT AllergyID, Allergy From Allergy";
$resultAllergy = $conn->query($sqlAllergy);



// Close the database connection
$conn->close();
?>

<html>
<head>
    <!-- Nav Bar Style Sheet -->
    <link rel="stylesheet" href="css/recipe.css">
    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <!-- Includes Inter Medium (font-weight: 500) and Inter Bold (font-weight: 700) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>
<body>
    <!-- Nav Bar -->
    <div class="bar">
        <ul>
            <div class="items">
                <li>
                    <span class="icon material-symbols-outlined">grocery</span>
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
    <!-- body -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <div class="creation_container">
        <div class="image_insert">
        <div class="file-input">
        <div class="file_first">
        <input type="file" name="file" id="file" class="file">
            <label for="file">+<p class="file-name"></p></label>
        </div>
        <h1>Add Your <br> Recipe Image</h1>
    </div>
        </div>
        <div class="recipe_info">
            <h1>Recipe Information</h1>
            <h2>Name of the Recipe</h2>
            <div class="recipe_name">
                <label class="name" for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="meal_type">
            <h2>Meal Types:</h2>
            <?php
            if ($resultMeal->num_rows > 0) {
                while ($rowMeal = $resultMeal->fetch_assoc()) {
                    echo '<input type="radio" name="meal_type" value="' . $rowMeal["TypeID"] . '" required>';
                    echo '<label>' . $rowMeal["Type"] . '</label>';
                }
            } else {
                echo "No meal types found.";
            }
            ?>
        </div>
            <div class="recipe_cuisine">
            <h2>Cuisine Types:</h2>
            <?php
            if ($resultCuisine->num_rows > 0) {
                while ($rowCuisine = $resultCuisine->fetch_assoc()) {
                    echo '<input type="radio" name="cuisine_type" value="' . $rowCuisine["TypeID"] . '" required>';
                    echo '<label>' . $rowCuisine["TypeName"] . '</label>';
                }
            } else {
                echo "No cuisine types found.";
            }
            ?>
        </div>
        <div class="recipe_ingredient">
    <h2> Ingredients <span class="fa fa-plus add"></span> </h2>
    <div class="appending_div">
        <div>
            <span class="lable">Name: </span>
            <input type="text" name="ingredient_name[]" required>
            &nbsp;
            <span class="lable">Quantities: </span>
            <input type="number" name="ingredient_quantity[]"  required>
            &nbsp;
            <span class="lable">Metric: </span>
            <select name="ingredient_metric[]" id="metric" style="width: 200px; height: 30px; border-radius: 7px;">
                <?php
                if ($resultMetrics->num_rows > 0) {
                    while ($rowMetric = $resultMetrics->fetch_assoc()) {
                        echo '<option value="' . $rowMetric["metric"] . '">' . $rowMetric["metric"] . '</option>';
                    }
                } else {
                    echo '<option value="">No metrics found</option>';
                }
                ?>
            </select>
        </div>
    </div>
</div>
<div class="recipe_steps">
    <h2> Steps <span class="fa fa-plus add1"></span> </h2>
    <div class="appending_div1">
        <div>
        <span class="steps">Steps 1: </span>
<textarea name="recipe_steps[]" rows="5" class="recipe-step" required></textarea>
<input type="hidden" name="step_numbers[]" value="1">
        </div>
    </div>
</div>
            <div class="recipe_calories">
            <h2>Recipe Calories</h2>
            <div class="calories">
                <label class="calories" for="calories">Calories: </label>
                <input type="text" id="calories" name="calories" required>
            </div>
            <div class="recipe_preference">
    <h2>Select Preference Type</h2>
    <?php
    if ($resultPreference->num_rows > 0) {
        while ($rowPreference = $resultPreference->fetch_assoc()) {
            echo '<input type="checkbox" name="Preference[]" value="' . $rowPreference["PreferenceID"] . '" >';
            echo '<label>' . $rowPreference["Preference"] . '</label>';
        }
    } else {
        echo "No Preference types found.";
    }
    ?>
</div>
<div class="recipe_allergy">
    <h2>Select Allergy Type</h2>
    <?php
    if ($resultAllergy->num_rows > 0) {
        while ($rowAllergy = $resultAllergy->fetch_assoc()) {
            echo '<input type="checkbox" name="Allergy[]" value="' . $rowAllergy["AllergyID"] . '" >';
            echo '<label>' . $rowAllergy["Allergy"] . '</label>';
        }
    } else {
        echo "No Allergy types found.";
    }
    ?>
</div>
            <div class="recipe_serving">
            <h2>Serving Number</h2>
            <div class="serving">
                <label class="serving" for="serving">Serving: </label>
                <input type="number" id="serving" name="serving" placeholder="1" required>
            </div>
            </div>
            <button class="savesub" id="recipeid" type="submit" onclick="showPopup()">SAVE&SUBMIT</button>
            <div class="popup" id="successPopup">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <p>Your recipe is successfully uploaded!</p>
            </div>
        </div>
 
</form>
    <!-- Nav Bar Javascript -->
    <script>
    /* nav bar */
        function openNav() {
    document.querySelector('#mySidenav').style.width = "250px"; 
    document.querySelector('.all-over-bkg').classList.add('is-visible');
  }
  
  function closeNav() {
    document.querySelector('#mySidenav').style.width = "0"; 
    document.querySelector('.all-over-bkg').classList.remove('is-visible');
  }
  
  document.querySelector('.openbtn').addEventListener('click', openNav);
  document.querySelector('.closebtn').addEventListener('click', closeNav);
  var input = document.getElementById('file');
        var infoArea = document.getElementById('file-name');

        input.addEventListener('change', showFileName);

        function showFileName(event) {
            var input = event.srcElement;
            var fileName = input.files[0].name;
            infoArea.textContent = 'File name: ' + fileName;
        }
  /* image insert */
  file.addEventListener('change', (e) => {
  // Get the selected file
  const [file] = e.target.files;
  // Get the file name and size
  const { name: fileName, size } = file;
  // Convert size in bytes to kilo bytes
  const fileSize = (size / 1000).toFixed(2);
  // Set the text content
  const fileNameAndSize = `${fileName} - ${fileSize}KB`;
  document.querySelector('.file-name').textContent = fileNameAndSize;
});
    /*ingredient field*/
    $(document).ready(function() {
    var i = 1;

    $('.add').on('click', function() {
        // Make an AJAX request to get metric options
        $.ajax({
            url: 'get_metrics.php',
            type: 'GET',
            dataType: 'json',
            success: function(options) {
                // Generate HTML for the metric dropdown
                var metricDropdown = '<span class="lable">Metric: </span>' +
                                     '<select name="ingredient_metric[]" style="width: 200px; height: 30px; border-radius: 7px;">';

                if (options.length > 0) {
                    for (var j = 0; j < options.length; j++) {
                        metricDropdown += '<option value="' + options[j] + '">' + options[j] + '</option>';
                    }
                } else {
                    metricDropdown += '<option value="">No metrics found</option>';
                }

                metricDropdown += '</select>';

                // Append the new ingredient row with metric dropdown
                var field = '<br><div>' +
                            '<span class="lable">Name: </span>' +
                            '<input type="text" name="ingredient_name[]" required> &nbsp; ' +
                            '<span class="lable">Quantities: </span>' +
                            '<input type="number" name="ingredient_quantity[]" required> &nbsp; ' +
                            metricDropdown +
                            '<button type="button" class="delete" onclick="deleteIngredient(this)">-</button></div>';

                $('.appending_div').append(field);
                i = i + 1;
            },
            error: function(xhr, status, error) {
                console.error('Error fetching metric options: ' + error);
            }
        });
    });

    // Function to delete ingredient input field
    deleteIngredient = function(element) {
        $(element).closest('div').remove();
    };
});
    /*Recipe Steps field */
    $(document).ready(function() {
    var i = 2; // Start with the next step number

    $('.add1').on('click', function() {
        var field = '<br><div><span class="steps">Steps ' + i + ': </span>' +
                    '<textarea name="recipe_steps[]" rows="5" class="recipe-step" required></textarea>' +
                    '<input type="hidden" name="step_numbers[]" value="' + i + '">' +
                    '<button type="button" class="delete" onclick="deleteStep(this)">-</button></div>';
        $('.appending_div1').append(field);
        i++;
    });

    // Function to delete recipe step input field
    deleteStep = function(element) {
        $(element).closest('div').remove();
        // Adjust step numbers after deletion
        $('.recipe-step').each(function(index) {
            $(this).siblings('input[name="step_numbers[]"]').val(index + 1);
            $(this).siblings('.steps').text('Steps ' + (index + 1) + ': ');
        });
    };
});
    /*pop up menu*/
    function showPopup() {
            document.getElementById('successPopup').style.display = 'block';
        }

        // Function to close the pop-up
    function closePopup() {
            document.getElementById('successPopup').style.display = 'none';
        }

    </script>
</body>
</html>