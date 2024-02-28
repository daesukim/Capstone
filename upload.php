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
        $insertCuisineTypeQuery = "INSERT INTO Cuisine_Type (TypeID, TypeName) VALUES (?, 'YourTypeName')";
        $stmt = $conn->prepare($insertCuisineTypeQuery);
        $stmt->bind_param("i", $cuisineType);
        $stmt->execute();
    }

    // Step 2: Insert into Recipe table
    $insertRecipeQuery = "INSERT INTO Recipe (cuisine_type, Name, servings, calories, meal_type, Url) 
                      VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insertRecipeQuery);

$successMessage = ""; // Initialize success message

if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0) {
    $target_dir = "../image/"; 
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Construct the URL for the uploaded image
        $fileUrl = 'https://cgi.luddy.indiana.edu/~mk19/capstone-team/team-25/project/image/' . basename($target_file);

        // Bind parameters with the updated $fileUrl
        $stmt->bind_param("isiiis", $cuisineType, $recipeName, $servings, $calories, $mealType, $fileUrl);
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit(); // Stop execution if file upload fails
    }
} else {
    // If no file is uploaded, insert recipe without the image URL
    $fileUrl = "";
    $stmt->bind_param("isiiis", $cuisineType, $recipeName, $servings, $calories, $mealType, $fileUrl);
}


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