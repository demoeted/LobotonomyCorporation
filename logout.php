<?php
    session_start();
    $_SESSION = [];
    $_SESSION['loggedIn'] = false;
    header('location: index.php');
    exit();
?>