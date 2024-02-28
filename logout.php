<?php
    // close the session and reinitialize the session array
    session_start();
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit;
?>