<?php
// db.php file is required to establish a connection with the database
require_once 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to handle friend requests.");
}

$receiver_id = $_SESSION['user_id']; // The logged-in user (who received the request)
$request_id = isset($_POST['request_id']) ? $_POST['request_id'] : null;
$action = isset($_POST['action']) ? $_POST['action'] : null;

if (!$request_id || !$action) {
    die("Error: Invalid request.");
}

// Verify that the friend request belongs to the logged-in user
$sql_check = "SELECT * FROM friend_requests WHERE request_id = ? AND receiver_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $request_id, $receiver_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die("Error: Friend request not found.");
}

// Handle the action (accept or decline)
if ($action === 'accept') {
    $update_request = "UPDATE friend_requests SET status = 'accepted' WHERE request_id = ?";
} elseif ($action === 'decline') {
    $update_request = "UPDATE friend_requests SET status = 'rejected' WHERE request_id = ?";
} else {
    die("Error: Invalid action.");
}

$stmt_update = $conn->prepare($update_request);
$stmt_update->bind_param("i", $request_id);

if ($stmt_update->execute()) {
    if ($action === 'accept') {
        echo "Follow request accepted!";
    }else{
        echo "Follow request declined.";
    }
} else {
    die("Error: Unable to update the request. " . $conn->error);
}

// Close the statement and connection
$stmt_check->close();
$stmt_update->close();
$conn->close();
