<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to view comments.");
}

$current_user_id = $_SESSION['user_id'];

// Fetch comments notifications
$comments_query = "
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.user_id = ? 
    ORDER BY c.created_at DESC
";
$stmt_comments = $conn->prepare($comments_query);
$stmt_comments->bind_param("i", $current_user_id);
$stmt_comments->execute();
$comments_notifications = $stmt_comments->get_result();

if ($comments_notifications->num_rows > 0) {
    echo "<h2>Comments</h2>";
    while ($comment = $comments_notifications->fetch_assoc()) {
        echo "<p>{$comment['username']} commented on your post.</p>";
    }
} else {
    echo "<p>No comments notifications.</p>";
}

$stmt_comments->close();
