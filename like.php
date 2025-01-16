<?php
// Database connection
$con = mysqli_connect("localhost", "root", "", "sociall"); // Update with your DB credentials

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

// Get the user_id of the logged-in user
$userLoggedIn = $_SESSION['username'];
$query = "SELECT id FROM users WHERE username = '$userLoggedIn'";
$result = mysqli_query($con, $query);
if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found.");
}
$row = mysqli_fetch_assoc($result);
$user_id = $row['id'];

// Check if post_id is set in the URL
if (!isset($_GET['post_id'])) {
    die("No post ID provided.");
}

$post_id = $_GET['post_id'];

// Sanitize the inputs
$post_id = mysqli_real_escape_string($con, $post_id);
$user_id = mysqli_real_escape_string($con, $user_id);

// Handle the like action
if (isset($_POST['like_btn'])) {
    // First, check if the user has already liked the post
    $check_like_query = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
    $like_result = mysqli_query($con, $check_like_query);

    if (mysqli_num_rows($like_result) == 0) {
        // Insert like into likes table
        $insert_query = "INSERT INTO likes (user_id, post_id) VALUES ('$user_id', '$post_id')";
        if (!mysqli_query($con, $insert_query)) {
            die("Error inserting like: " . mysqli_error($con));
        }

        // Increment likes in posts table
        $like_query = "UPDATE post SET likes = likes + 1 WHERE id = '$post_id'";
        if (!mysqli_query($con, $like_query)) {
            die("Error updating likes: " . mysqli_error($con));
        }
    }
} elseif (isset($_POST['unlike_btn'])) {
    // First, check if the user has already liked the post
    $check_like_query = "SELECT * FROM likes WHERE user_id = '$user_id' AND post_id = '$post_id'";
    $like_result = mysqli_query($con, $check_like_query);

    if (mysqli_num_rows($like_result) > 0) {
        // Remove like from likes table
        $delete_query = "DELETE FROM likes WHERE user_id='$user_id' AND post_id='$post_id'";
        if (!mysqli_query($con, $delete_query)) {
            die("Error deleting like: " . mysqli_error($con));
        }

        // Decrement likes in posts table
        $unlike_query = "UPDATE post SET likes = likes - 1 WHERE id = '$post_id'";
        if (!mysqli_query($con, $unlike_query)) {
            die("Error updating likes: " . mysqli_error($con));
        }
    }
}

// Redirect back to home page after like/unlike action
header("Location: home.php");
exit();
