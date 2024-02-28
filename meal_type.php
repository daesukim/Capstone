<html>
<head>
    <!-- Nav Bar Style Sheet -->
    <link rel="stylesheet" href="css/meal_type.css">
    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
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
                <li><a href="index.html">Landing Page</a></li>
                <li><a href="team.html">About Us</a></li>
                <li><a href="project.html">About the Project</a></li>
                <li><a href="video.html">Promotional Video</a></li>
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
        <div class = "card breakfast">
            <div class = "text-box">
                <p> Breakfast </p>
            </div>
        </div>
        <div class = "card lunch">
            <div class = "text-box">
                <p> Lunch </p>
            </div>
        </div>
        <div class = "card dinner">
            <div class = "text-box">
                <p> Dinner </p>
            </div>
        </div>
        <?php
            // if user logged in, display this page. Otherwise, send the user back to login.php
            session_start();
            if (isset($_SESSION['user_id'])) {
                echo "ID: " . $_SESSION['user_id'] . "<br>";
                echo "First Name: " . $_SESSION['first_name'] . "<br>";
                echo "Last Name: " . $_SESSION['last_name'] . "<br>";
            }
            else{
                header("Location: login.php");
            }
        ?>
    </div>
</body>
</html>