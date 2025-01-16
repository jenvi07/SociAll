<?php
session_start();
include 'db.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$post = null; // Initialize $post
$post_id = null; // Initialize $post_id
$friends_result = null; // Initialize $friends_result

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Query to fetch the post details
    $post_query = "SELECT * FROM posts WHERE post_id = '$post_id'";
    $post_result = mysqli_query($conn, $post_query);

    // Check if the query was successful
    if ($post_result && mysqli_num_rows($post_result) > 0) {
        // Fetch the post details
        $post = mysqli_fetch_assoc($post_result);
    } else {
        $post = null; // Post not found
    }

    // Query to fetch accepted friends from friend_requests table
    $friends_query = "SELECT u.id, u.username 
                      FROM users u 
                      JOIN friend_requests f ON (f.sender_id = u.id OR f.receiver_id = u.id)
                      WHERE (f.sender_id = '$user_id' OR f.receiver_id = '$user_id') 
                      AND u.id != '$user_id' 
                      AND f.status = 'accepted'";
    $friends_result = mysqli_query($conn, $friends_query);

    if (!$friends_result) {
        die("Error fetching friends: " . mysqli_error($conn));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['post_id'])) {
            $post_id = $_POST['post_id'];
            echo "Post ID received: " . $post_id; // For debugging
        } else {
            echo "Post ID not received!";
        }
    }
    

    // Handle sharing the post with selected friends
    if (isset($_POST['share']) && isset($_POST['friend_ids'])) {
        $friend_ids = $_POST['friend_ids'];
        $post_id = $_POST['post_id'];        // The ID of the post being shared
        $user_id = $_SESSION['user_id'];     // Assuming the logged-in user's ID is in session

        foreach ($friend_ids as $friend_id) {
            // Insert shared post data into the messages table with post_id
            $share_query = "INSERT INTO messages (sender_id, receiver_id, message, post_id, created_at, read_status) 
                            VALUES ('$user_id', '$friend_id', 'Shared a post', '$post_id', NOW(), 0)";
            if(!mysqli_query($conn, $share_query)){
                die("Error sharing post:" .mysqli_error($conn));
            }
        }

        echo "Post successfully shared with selected friends!";
    }
} else {
    echo "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Post with Friends</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .post-content {
            background-color: #fafafa;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .friend-list {
            margin-bottom: 20px;
        }

        .friend-list label {
            display: block;
            margin-bottom: 10px;
        }

        .friend-list input[type="checkbox"] {
            margin-right: 10px;
        }

        .share-button {
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .share-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Share Post with Friends</h2>

        <!-- Display the post content -->
        <div class="post-content">
            <?php if ($post): ?>
                <h3><?php echo htmlspecialchars($post['text_content']); ?></h3>
                <?php if ($post['media_type'] === 'image'): ?>
                <p><?php echo htmlspecialchars($post['media_file']); ?></p>
                <?php elseif ($post['media_type'] === 'video'): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($post['media_file']); ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
            <?php else: ?>
                <p>Post not found or unable to load post details.</p>
            <?php endif; ?>
        </div>

        <!-- Form to select friends and share the post -->
        <form id="share-form-<?php echo $post['id']; ?>" action="share.php" method="post">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="share" value="1">
            <div class="friend-list">
                <h4>Select friends to share with:</h4>

                <?php if ($friends_result && mysqli_num_rows($friends_result) > 0): ?>
                    <?php while ($friend = mysqli_fetch_assoc($friends_result)): ?>
                        <label>
                            <input type="checkbox" name="friend_ids[]" value="<?php echo $friend['id']; ?>">
                            <?php echo htmlspecialchars($friend['username']); ?>
                        </label>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No friends found.</p>
                <?php endif; ?>
            </div>
            <button type="button" onclick="sharePost(<?php echo $post['id']; ?>)">Share</button>
            
        </form>
    </div>
</body>
</html>
