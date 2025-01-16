<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user settings from the database
$user_query = "SELECT theme FROM users WHERE id='$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Define theme class based on user preference
$themeClass = '';
if ($user['theme'] == 'dark') {
    $themeClass = 'dark-theme';
} else {
    $themeClass = 'light-theme';
}
