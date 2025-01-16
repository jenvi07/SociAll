<?php
session_start();
require_once 'db.php';  // Ensure the database connection

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to follow/unfollow users.");
}

$sender_id = $_SESSION['user_id'];  // Ensure session user ID is valid
$receiver_id = isset($_POST['receiver_id']) ? $_POST['receiver_id'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : null;

if (!$receiver_id || !$action) {
    die("<div class='error-message'>Error: Invalid request. No user or action specified.</div>");
}

if ($action === 'follow') {
    // Check if a follow request already exists
    $check_request = "SELECT * FROM friend_requests WHERE sender_id = ? AND receiver_id = ?";
    $stmt_check = $conn->prepare($check_request);
    $stmt_check->bind_param("ii", $sender_id, $receiver_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        // Insert new follow request if no request exists
        $insert_request = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
        $stmt_insert = $conn->prepare($insert_request);
        $stmt_insert->bind_param("ii", $sender_id, $receiver_id);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();
} elseif ($action === 'unfollow') {
    // Unfollow by deleting the follow request or friendship
    $delete_request = "DELETE FROM friend_requests WHERE sender_id = ? AND receiver_id = ?";
    $stmt_delete = $conn->prepare($delete_request);
    $stmt_delete->bind_param("ii", $sender_id, $receiver_id);
    $stmt_delete->execute();
    $stmt_delete->close();
}

// Close the database connection and return the user to the same page
$conn->close();
header("Location: .php");  // Redirect as needed
exit;
