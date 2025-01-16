<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view likes.");
}

$current_user_id = $_SESSION['user_id'];

// Fetch likes notifications
$likes_query = "
    SELECT l.*, u.username 
    FROM likes l 
    JOIN users u ON l.user_id = u.id 
    WHERE l.user_id = ? 
    ORDER BY l.created_at DESC
";
$stmt_likes = $conn->prepare($likes_query);
$stmt_likes->bind_param("i", $current_user_id);
$stmt_likes->execute();
$likes_notifications = $stmt_likes->get_result();

if ($likes_notifications->num_rows > 0) {
    echo "<h2>Likes</h2>";
    while ($like = $likes_notifications->fetch_assoc()) {
        echo "<p>{$like['username']} liked your post.</p>";
    }
} else {
    echo "<p>No likes notifications.</p>";
}

$stmt_likes->close();
