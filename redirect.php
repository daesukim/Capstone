<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://accounts.google.com/gsi/client" async></script>
    <link rel="stylesheet" href="css/login.css">
    <title>Google Login API</title>
</head>
<body>
    <div class = "container">
        <div id="g_id_onload"
            data-client_id="178800428439-qnrr2117sqsl1ko87og00ldsnk4sgs9j.apps.googleusercontent.com"
            data-context="use"
            data-ux_mode="popup"
            data-login_uri="https://cgi.luddy.indiana.edu/~team25/project/login.php"
            data-auto_prompt="false">
        </div>

        <div class="g_id_signin"
            data-type="standard"
            data-shape="rectangular"
            data-theme="outline"
            data-text="signin_with"
            data-size="large"
            data-logo_alignment="left">
        </div>

        <button id="logout">Logout</button>

        <?php
            session_start();
            if (isset($_SESSION['user_id'])) {
                echo "ID: " . $_SESSION['user_id'] . "<br>";
                echo "User Name: " . $_SESSION['user_name'] . "<br>";
            }
            else{
                echo "<h1> Session does not exist </h1>";
            }
        ?>
    </div>
    <script>
        var logout = document.getElementById('logout');
        logout.addEventListener('click', function() {
            window.location.href = 'https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://cgi.luddy.indiana.edu/~team25/project/logout.php';
        });
    </script>
</body>
</html>