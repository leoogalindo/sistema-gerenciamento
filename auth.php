<?php
/**
 * Page Authentication
 */
session_start();
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0 && (stristr($_SERVER['PHP_SELF'],'login.php') ||stristr($_SERVER['PHP_SELF'],'registration.php'))){
    /**
     * Redirect to index page if user is already logged in and browsing the login and registration Page
     */
    header("Location:./");
    exit;
}else if((!isset($_SESSION['user_id']) || (isset($_SESSION['user_id']) && $_SESSION['user_id'] <= 0)) && (!stristr($_SERVER['PHP_SELF'],'login.php') && !stristr($_SERVER['PHP_SELF'],'registration.php'))){
    /**
     * Redirect to login page if user is not logged in and not in login or registration Page
     */
    header("Location:./login.php");
    exit;
}