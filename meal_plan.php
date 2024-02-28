<html>
<head>
    <title>
        View Meal Plan
    </title>

    <!-- Style Sheet -->
    <link rel="stylesheet" href="css/meal_plan_styles.css">
    <!-- Nav Bar Google Icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body onload="toggleCheck()">

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

    <!-- Pagination Javascript -->
    <script src="js/meal_plan.js"></script>

    <!-- Prevent form resubmission on refresh -->
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>

    <!-- Javascript to submit form -->
    <script>
        function submitForm(recipeID) {
            document.getElementById("individualRecipe" + recipeID).submit();
        }
    </script>

    <!-- Toggle Button Javascript -->
    <script>
        function toggleCheck() {
            if(document.getElementById("myCheckbox").checked === true){
                document.getElementById("weekly").style.display = 'block';
                document.getElementById("daily").style.display = 'none';
            } else {
                document.getElementById("weekly").style.display = 'none';
                document.getElementById("daily").style.display = 'block';
            }
        }
    </script>

    <!-- Top Buttons -->
    <div class="topContent">
        <div class="topButtons">

            <!-- Drop down for meal plan dates -->
            <?php
                // Sessions
                session_start();
                if (!isset($_SESSION['user_id'])) {
                    header("Location: login.php");
                    exit();
                    //$_SESSION['user_id'] = 1234567;
                }
                
                // Establish connection
                $servername = "db.luddy.indiana.edu";
                $username = "i494f23_team25";
                $password = "my+sql=i494f23_team25";
                $dbname = "i494f23_team25";
                $con = mysqli_connect($servername, $username, $password, $dbname);

                if (!$con) { die("Failed to connect to MySQL: " . mysqli_connect_error); }

                // Getting meal plan dates
                $mp_query = "SELECT mp.startDate AS 'startDate', mp.endDate AS 'endDate'
                FROM Meal_Plan AS mp
                JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
                JOIN User AS u ON ump.userID = u.userID
                WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "' ORDER BY mp.endDate DESC";

                $meal_plan_res = mysqli_query($con, $mp_query);
                
                echo '<div class="calGrocery">
                <button class="button cal hvr-grow" onclick="handleAuthClick()">Export to <span class="material-symbols-outlined calendar">calendar_month</span></button>
                <form name="groceryList" id="groceryList" action="grocerylist.php" method="post">';
                    if (!empty($_POST['planDates'])) {
                        $glStart = mysqli_real_escape_string($con, $_POST['planDates']);
                        echo '<input type="hidden" value="' . $glStart . '" name="glStart" id="glStart">';
                    }
                echo '<button type="submit" class="button small hvr-grow">Grocery List</button>
                </form>
            </div>';

                // Form to set start date
                echo '<form name="chosenPlan" id="chosenPlan" action="" method="post">
                    <input type="hidden" id="planDates" name="planDates" value="">
                </form>';

            ?>

        </div>

        <!-- Toggle -->
        <div class="toggle">
            <p class="toggleText">Daily</p>
            <label class="switch">
                <input type="checkbox" id="myCheckbox" onchange="toggleCheck()" checked>
                <span class="slider round"></span>
            </label>
            <p class="toggleTextW">Weekly</p>
        </div>
    </div>

    <!-- Page Content -->
    <div id="weekly" style="display: none;"><?php include 'weekly.php' ?></div>
    <div id="daily" style="display: none;"><?php include 'daily.php' ?></div>

    
    <!-- Google Calendar Javacript -->
    <script type="text/javascript">

      // TODO(developer): Set to client ID and API key from the Developer Console
      const CLIENT_ID = '828989521203-7o526jbctav40hu0ap9jqtkps2vas3u5.apps.googleusercontent.com';
      const API_KEY = 'AIzaSyAJlCSgelE5ETo20-T220cIbbzu8M4qVvA';

      // Discovery doc URL for APIs used by the quickstart
      const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest';

      // Authorization scopes required by the API; multiple scopes can be
      // included, separated by spaces.
      const SCOPES = 'https://www.googleapis.com/auth/calendar';

      let tokenClient;
      let gapiInited = false;
      let gisInited = false;

      /**
       * Callback after api.js is loaded.
       */
      function gapiLoaded() {
        gapi.load('client', initializeGapiClient);
      }

      /**
       * Callback after the API client is loaded. Loads the
       * discovery doc to initialize the API.
       */
      async function initializeGapiClient() {
        await gapi.client.init({
          apiKey: API_KEY,
          discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
      }

      /**
       * Callback after Google Identity Services are loaded.
       */
      function gisLoaded() {
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: CLIENT_ID,
          scope: SCOPES,
          callback: '', // defined later
        });
        gisInited = true;
      }

       /**
       *  Sign in the user upon button click.
       */
      function handleAuthClick() {
        tokenClient.callback = async (resp) => {
          if (resp.error !== undefined) {
            throw (resp);
          }
          await addEvent();
        };

        if (gapi.client.getToken() === null) {
          // Prompt the user to select a Google Account and ask for consent to share their data
          // when establishing a new session.
          tokenClient.requestAccessToken({prompt: 'consent'});
        } else {
          // Skip display of account chooser and consent dialog for an existing session.
          tokenClient.requestAccessToken({prompt: ''});
        }
      }

    // Refer to the JavaScript quickstart on how to setup the environment:
    // https://developers.google.com/calendar/quickstart/js
    // Change the scope to 'https://www.googleapis.com/auth/calendar' and delete any
    // stored credentials.
    async function addEvent() {
        <?php
            // Check if date has been selected 
            if (!empty($_POST['planDates'])) {
                $calStart = mysqli_real_escape_string($con, $_POST['planDates']);
                if ($calStart == 'No Meal Plans Available') {
                    $calStart = '';
                }
            } else { 
                $get_calStart = "SELECT mp.startDate AS 'startDate', mp.endDate AS 'endDate'
                FROM Meal_Plan AS mp
                JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
                JOIN User AS u ON ump.userID = u.userID
                WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "' ORDER BY mp.endDate DESC LIMIT 1";

                $run_calStart = mysqli_query($con, $get_calStart);
                $calStart_res = mysqli_fetch_assoc($run_calStart);
                $calStart = $calStart_res['startDate'];
            }

            // If there is a meal plan
            if (!empty($calStart)) {
            // Get start date and end date
            $start_date_conv = strtotime($calStart);
            $start_date_num = date('w', $start_date_conv);
            $end_date_conv = strtotime("+7 day", $start_date_conv);
            $end_date = date("Y-m-d", $end_date_conv);

            // Get array of dates between start and end date ordered from Sunday to Saturday
            $starting = date_create($calStart);
            $ending = date_create($end_date);
            $interval = DateInterval::createFromDateString('1 day');
            $daterange = new DatePeriod($starting, $interval ,$ending);

            $unordered_dt = array();
            foreach ($daterange as $date) {
                 $date_conv = date("Y-m-d", $end_date_conv);
                 array_push($unordered_dt, $date);
            }
            
            $ordered_dt = array();
            $ordered_dates = array();
            $counter = 0;
            while (count($ordered_dt) < 7) {
                 if ($counter > 6) { $counter = 0; }
                 $unformat = $unordered_dt[$counter];
                 $just_date = date_format($unformat, "Y-m-d");
                 $curr = strtotime($just_date);
                 $day_num = date('w', $curr);

                 if ($day_num == count($ordered_dt)) {
                     array_push($ordered_dt, $unformat);
                     array_push($ordered_dates, $just_date);
                 }

                 $counter = $counter + 1;
            }

            // Get meal plan
            $get_plan = "SELECT r.Name AS 'name', mpe.Chosen_Date AS 'day', r.meal_type AS 'type'
            FROM Recipe AS r
            LEFT JOIN Meal_Plan_Event AS mpe ON mpe.recipeID = r.RecipeID
            LEFT JOIN Meal_Plan AS mp ON mpe.mealplanID = mp.PlanID
            LEFT JOIN user_mealplan AS ump ON ump.mealPlanID = mp.PlanID
            LEFT JOIN User AS u ON ump.userID = u.UserID
            WHERE u.GoogleAuth = '" . $_SESSION['user_id'] . "'
            AND mp.startDate = '" . $calStart . "'
            ORDER BY mpe.Chosen_Date, r.meal_type";

            $run_get_plan = mysqli_query($con, $get_plan);

            // For each recipe in meal plan
            for ($x=0; $x<mysqli_num_rows($run_get_plan); $x++) {
                // Get next recipe 
		$event = mysqli_fetch_assoc($run_get_plan);
            	
		// Set type and start/end time for each meal type
		$type = "";
                if ($event['type'] == 1) { 
                    $type = "Breakfast"; 
                    $start_time = 'T08:00:00-05:00';
                    $end_time = 'T09:00:00-05:00';
                }
                else if ($event['type'] == 2) { 
                    $type = "Lunch"; 
                    $start_time = 'T12:00:00-05:00';
                    $end_time = 'T13:00:00-05:00';
                }
                else { 
                    $type = "Dinner"; 
                    $start_time = 'T16:00:00-05:00';
                    $end_time = 'T17:00:00-05:00';
                }

		// Get day of week number of current day 
                $dayNum = $event['day'];

                // Get timezone
                $timezone_conv = date_timezone_get($ordered_dt[$dayNum]);
                $timezone = timezone_name_get($timezone_conv);

                // Get redirect link
                $linkDate = strtotime($ordered_dates[$dayNum]);
                $linkStr = date('Y/n/j', $linkDate);
                $link = "https://calendar.google.com/calendar/u/0/r/week/" . $linkStr;
                echo "console.log('" . $link . "');\n";

                // Create event object
                echo "var event" . $x . " = {\n";
                echo "'summary': '" . $type . ": " . $event['name'] . "',\n";
                echo "'start': { 'dateTime': '" . $ordered_dates[$dayNum] . $start_time . "', 'timeZone': '" . $timezone . "' },\n";
                echo "'end': { 'dateTime': '" . $ordered_dates[$dayNum] . $end_time . "', 'timeZone': '" . $timezone . "' }\n";
                echo "};\n";

               // Insert event into user's primary google calendar
                echo "var request" . $x . " = gapi.client.calendar.events.insert({
                'calendarId': 'primary',
                'resource': event" . $x . "
                });\n";

                // Exectute request and print event link to console
                echo "request" . $x . ".execute(function(event" . $x . ") {
                    console.log('Event created: ' + event" . $x . ".htmlLink);
                });\n";
            } 
            // Redirect Link
            echo "window.location.href = '" . $link . "';";
	}	        
	?>
    }
    </script>
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>

</body>
</html>
