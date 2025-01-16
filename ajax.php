<?php
include('db.php');
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Like/Unlike Post
if (isset($_POST['action']) && $_POST['action'] == 'like_post') {
    $post_id = $_POST['post_id'];

    // Check if the user has already liked the post
    $check_query = "SELECT * FROM likes WHERE user_id='$user_id' AND post_id='$post_id'";
    $result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // Unlike the post
        $delete_query = "DELETE FROM likes WHERE user_id='$user_id' AND post_id='$post_id'";
        mysqli_query($conn, $delete_query);

        // Decrease like count
        $update_query = "UPDATE posts SET like_count = like_count - 1 WHERE id='$post_id'";
        mysqli_query($conn, $update_query);

        echo json_encode(['liked' => false, 'post_id' => $post_id]);
    } else {
        // Like the post
        $insert_query = "INSERT INTO likes (user_id, post_id) VALUES ('$user_id', '$post_id')";
        mysqli_query($conn, $insert_query);

        // Increase like count
        $update_query = "UPDATE posts SET like_count = like_count + 1 WHERE id='$post_id'";
        mysqli_query($conn, $update_query);

        echo json_encode(['liked' => true, 'post_id' => $post_id]);
    }
}

    // Handle Follow/Unfollow
if (isset($_POST['action']) && ($_POST['action'] == 'follow' || $_POST['action'] == 'unfollow')) {
    $profile_user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    if ($_POST['action'] == 'follow') {
        // Follow logic
        $follow_query = "INSERT INTO friend_requests (sender_id, receiver_id, status, created_at) VALUES ('$user_id', '$profile_user_id', 'pending', NOW())";
        if (mysqli_query($conn, $follow_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not follow user.']);
        }
    } elseif ($_POST['action'] == 'unfollow') {
        // Unfollow logic
        $unfollow_query = "DELETE FROM friend_requests WHERE sender_id = '$user_id' AND receiver_id = '$profile_user_id'";
        if (mysqli_query($conn, $unfollow_query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Could not unfollow user.']);
        }
    }
}


// Handle Notifications Check
if (isset($_POST['action']) && $_POST['action'] == 'check_notifications') {
    $query = "SELECT COUNT(*) as new_notifications FROM notifications WHERE user_id='$user_id' AND seen=0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['new_notifications' => $row['new_notifications']]);
}

// Handle Messages Check
if (isset($_POST['action']) && $_POST['action'] == 'check_messages') {
    $query = "SELECT COUNT(*) as new_messages FROM messages WHERE recipient_id='$user_id' AND is_read=0";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['new_messages' => $row['new_messages']]);
}
