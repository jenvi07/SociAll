<?php
// list_users.php
include 'db.php';

session_start();
$logged_in_user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch all users except the logged-in user
$sql_users = "SELECT id, username FROM users WHERE id != ?";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->bind_param("i", $logged_in_user_id);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        echo '<div>';
        echo '<span>' . htmlspecialchars($row['username']) . '</span>';
        echo '<a href="send_request.php?receiver_id=' . $row['id'] . '">Follow</a>'; // Link to send follow request
        echo '</div>';
    }
} else {
    echo "No users found.";
}
