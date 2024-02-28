<html>
<head>
    <!-- Nav Bar Style Sheet -->
    <link rel="stylesheet" href="css/landing.css">
    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
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

    <!-- Main Content -->
    <div class="top">
        <div class="top-left">
            <h1 class="topText">Meal Planning Made Easy</h1>
            <p class="topDescr">Seamlessly plan meals, shop efficiently, and savor every bite with personalized recipes and automatic grocery lists at your fingertips.</p>
            <a href="login.php"><button class="hvr-grow">Sign Up</button></a>
        </div>
        <img src="image/meal_plan.png" alt="meal plan image" class="topImg">
    </div>

    <div class="container">
        <div class="missionDiv">
            <div class="mission">
                <img src="image/veg_spill.png" alt="our mission image" class="missionImg">
                <p class="subtext"> Our mission is to reduce personal food waste by providing users with a platform that simplifies the process of meal planning and grocery shopping.</p>
            </div>
        </div>
        <div class="item">
            <img src="image/guru.png" alt="meal planning image" class="image">
            <div class="textBlockR">
                <p class="titleR"> Effortless Meal Planning </p>
                <p class="subtextR">Users can choose from GreenGrocer's wide array of recipes, of varying cuisine types, and easily add their desired recipes to their meal plan for the week. And for those who are more interested in meal planning for health purposes, GreenGrocer also provides a calorie conscious meal plan option. This allows users to have a weekly meal plan randomly generated for them based on a calculated daily calorie intake.</p>
            </div>
        </div>
        <div class="itemL">
            <div class="textBlockL">
                <p class="titleL"> Convenient Calendar Integration </p>
                <p class="subtextL"> At GreenGrocer, we&apos;re all about simplifying the meal planning process for our users. That&apos;s why our system gives users the option to export their meal plan to their Google Calendar for easy viewing throughout the week.  </p>
            </div>
            <div><img src="image/calendar.png" alt="meal planning image" class="image"></div>
        </div>
        <div class="item" style="margin-bottom: 75px;">
            <img src="image/list.png" alt="meal planning image" class="image">
            <div class="textBlockR">
                <p class="titleR"> Grocery List Generation </p>
                <p class="subtextR"> Once a user has created a meal plan, GreenGrocer then generates a grocery list with all the ingredients they will need to cook their meals for the week. Not only does this save the user the time it takes to compile the list themselves, but it also reduces their potential for overbuying. By reducing overbuying in users, GreenGrocer works to reduce personal food waste that occurs from a lack of planning before buying.  </p>
            </div>
        </div>
    </div>

</body>
</html>
