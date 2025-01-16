<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view friend requests.");
}

$receiver_id = $_SESSION['user_id']; // The logged-in user

// Fetch pending follow requests
$sql_requests = "SELECT friend_requests.request_id, users.username 
                 FROM friend_requests 
                 JOIN users ON friend_requests.sender_id = users.id 
                 WHERE friend_requests.receiver_id = ? AND friend_requests.status = 'pending'";

$stmt_requests = $conn->prepare($sql_requests);
$stmt_requests->bind_param("i", $receiver_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();

if ($result_requests->num_rows > 0) {
    echo "<h2>Pending Friend Requests</h2>";
    while ($row = $result_requests->fetch_assoc()) {
        echo "<p>{$row['username']} has sent you a follow request.</p>";
        echo "<form method='POST' action='handle_requests.php'>
                <input type='hidden' name='request_id' value='{$row['request_id']}'>
                <button class='btn' type='submit' name='action' value='accept'>Accept</button>
                <button class='btn' type='submit' name='action' value='decline'>Decline</button>                
              </form>";
    }
} else {
    echo "You have no pending friend requests.";
}

$stmt_requests->close();
?>
<style>
    .btn{
        background-color: #8c675a;
        color: #dfd8c9;
        border-radius: 5px;
        border: 2px solid #8c675a;
    }

    .btn:hover {
        background-color: #dfd8c9;
        color: #8c675a;
        border-radius: 5px;
        border: 2px solid #dfd8c9;
    }
</style>