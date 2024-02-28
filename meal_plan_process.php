<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $start_date = $_POST["start_date"];
        $end_date = date('Y-m-d', strtotime($start_date . ' + 6 days'));

        $conn = mysqli_connect("db.luddy.indiana.edu","i494f23_team25","my+sql=i494f23_team25","i494f23_team25");
        if (!$conn) { die("Connection failed: " . mysqli_connect_error());}

        $checkDuplicateStartDate = "SELECT *
                                    FROM Meal_Plan AS mp
                                    JOIN user_mealplan AS um ON um.mealPlanID = mp.PlanID
                                    JOIN User AS u ON um.userID = u.UserID
                                    WHERE u.GoogleAuth = " . $_SESSION['user_id'] . " AND mp.startDate = '" . $start_date . "'";
        $CheckResult = mysqli_query($conn, $checkDuplicateStartDate);
        if(mysqli_num_rows($CheckResult) > 0) {
            $_SESSION['error_message'] = "You already have the same meal plan";
            header("Location: add_page.php?menu_id=" . $_GET['menuID']);
        }
        else{
            $sql = "INSERT INTO Meal_Plan (startDate, endDATE) VALUES ('$start_date', '$end_date')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                $plan_id = mysqli_insert_id($conn);
                $userID;
                
                // getting userID matched with googleID
                $sql2 = "SELECT UserID FROM User WHERE GoogleAuth = " . $_SESSION['user_id'];
                $result2 = mysqli_query($conn, $sql2);
                while ($row = mysqli_fetch_assoc($result2)) {
                    $userID = $row["UserID"];
                }

                $sql3 = "INSERT INTO user_mealplan (userID, mealPlanID) VALUES ($userID, $plan_id)";
                $result3 = mysqli_query($conn, $sql3);
                header("Location: add_page.php?menu_id=" . $_GET['menuID']);
                exit;
            }
        }
    }
?>