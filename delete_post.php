<?php
session_start();
include 'db.php';

if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Delete the post from the database
    $delete_query = "DELETE FROM posts WHERE post_id='$post_id' AND user_id='{$_SESSION['user_id']}'";
    if (mysqli_query($conn, $delete_query)) {
        header('Location: dashboard.php');
    } else {
        echo "Error deleting post: " . mysqli_error($conn);
    }
} else {
    header('Location: dashboard.php');
}
