<?php
session_start();
require 'db.php'; // Database connection

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];

// Check if a user_id is passed in the URL
if (!isset($_GET['user_id'])) {
    die("No user specified.");
}

$profile_user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

// Fetch the profile user details
$user_query = "SELECT * FROM users WHERE id='$profile_user_id'";
$user_result = mysqli_query($conn, $user_query);
if (!$user_result || mysqli_num_rows($user_result) === 0) {
    die("User not found.");
}
$profile_user = mysqli_fetch_assoc($user_result);

// Check if the logged-in user is already following the profile user
$follow_check_query = "SELECT * FROM friend_requests WHERE sender_id = '$logged_in_user_id' AND receiver_id = '$profile_user_id' AND status = 'accepted'";
$follow_check_result = mysqli_query($conn, $follow_check_query);
$is_following = mysqli_num_rows($follow_check_result) > 0;

// Handle follow/unfollow functionality
if (isset($_POST['follow_action'])) {
    if ($is_following) {
        // Unfollow logic
        $unfollow_query = "DELETE FROM friend_requests WHERE sender_id = '$logged_in_user_id' AND receiver_id = '$profile_user_id'";
        mysqli_query($conn, $unfollow_query);
        $is_following = false;
    } else {
        // Follow logic
        $follow_query = "INSERT INTO friend_requests (sender_id, receiver_id, status, created_at) VALUES ('$logged_in_user_id', '$profile_user_id', 'pending', NOW())";
        mysqli_query($conn, $follow_query);
        $is_following = true;
    }

    // Reload the page to update the follow button
    header("Location: display_profile.php?user_id=$profile_user_id");
    exit();
}

// Fetch account visibility
$is_private = $profile_user['visibility'] === 'private'; // Assuming visibility is a column in the users table

// Fetch the followers count
$followers_query = "SELECT COUNT(*) AS follower_count FROM friend_requests WHERE receiver_id = '$profile_user_id' AND status = 'accepted'";
$followers_result = mysqli_query($conn, $followers_query);
$follower_count = mysqli_fetch_assoc($followers_result)['follower_count'];

// Fetch the following count
$following_query = "SELECT COUNT(*) AS following_count FROM friend_requests WHERE sender_id = '$profile_user_id' AND status = 'accepted'";
$following_result = mysqli_query($conn, $following_query);
$following_count = mysqli_fetch_assoc($following_result)['following_count'];

// Fetch the friends count
$friends_query = "SELECT COUNT(*) AS friends_count FROM friend_requests 
                  WHERE (sender_id = '$profile_user_id' OR receiver_id = '$profile_user_id') 
                  AND status = 'accepted'";
$friends_result = mysqli_query($conn, $friends_query);
$friends_count = mysqli_fetch_assoc($friends_result)['friends_count'];

// Fetch the user's posts if the account is not private or the user is a follower
if (!$is_private || $is_following) {
    $post_query = "SELECT post.*, 
                          (SELECT COUNT(*) FROM likes WHERE post_id = post.id) AS like_count,
                          (SELECT COUNT(*) FROM likes WHERE post_id = post.id AND user_id = '$logged_in_user_id') AS user_liked
                   FROM post 
                   WHERE post.user_id = '$profile_user_id' 
                   ORDER BY post.created_at DESC";
    $post_result = mysqli_query($conn, $post_query);
} else {
    $post_result = null; // If private, and not a friend or follower, do not fetch posts
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" text_text_text_text_text_content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile_user['name']); ?>'s Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Profile Header Styles */
        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #8c756a;
            opacity: 0.7;
            padding: 20px;
            border-radius: 10px;
            margin: 10px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            color: #dfd8c9;
        }

        .profile-info img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .profile-info h1, .profile-info p {
            margin: 0;
        }

        .bio {
            font-style: italic;
            margin-top: 5px;
        }

        .stats {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item a {
            text-decoration: none;
            color: #dfd8c9;
        }

        .stat-item a:hover {
            text-decoration: underline;
        }

        .stat-item strong {
            display: block;
            font-size: 18px;
            font-weight: bold;
        }

        /* Follow/Unfollow Button Styles */
        .follow-btn {
            background-color: #dfd8c9;
            color: #8c576a;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .follow-btn:hover {
            background-color: #e8e0d1;
        }

        .post-container {
            margin-top: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
        }

        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .post img, .post video {
            max-width: 100%;
        }

        .like-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #007bff;
        }
    </style>

</head>
<body>
<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-info">
        <img src="<?php echo $profile_user['profile_pic'] ?: 'default.jpg'; ?>" alt="Profile Picture">
        <div>
            <h1><?php echo htmlspecialchars($profile_user['name']); ?></h1>
            <p>@<?php echo htmlspecialchars($profile_user['username']); ?></p>
            <p class="bio"><?php echo htmlspecialchars($profile_user['bio']); ?></p>

            <!-- Followers, Following, and Friends -->
            <div class="stats">
                <div class="stat-item">
                    <a href="#">
                        <strong><?php echo $follower_count; ?></strong>Followers
                    </a>
                </div>
                <div class="stat-item">
                    <a href="#">
                        <strong><?php echo $following_count; ?></strong>Following
                    </a>
                </div>
                <div class="stat-item">
                    <a href="#">
                        <strong><?php echo $friends_count; ?></strong>Friends
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Follow/Unfollow Button -->
<?php if ($profile_user_id != $logged_in_user_id): ?>
    <button id="follow-btn" class="follow-btn" data-following="<?php echo $is_following ? 'true' : 'false'; ?>">
        <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
    </button>
<?php endif; ?>

</div>

<!-- Posts Section -->
<div class="post-container">
<h2><?php echo htmlspecialchars($profile_user['name']); ?>'s Posts</h2>

<?php if ($is_private && !$is_following): ?>
    <p>This account is private. Follow to view their posts and followers.</p>
<?php elseif ($post_result && mysqli_num_rows($post_result) > 0): ?>
    <?php while ($post = mysqli_fetch_assoc($post_result)): ?>
        <div class="post">
            <p><?php echo htmlspecialchars($post['text_content']); ?></p>

            <?php if ($post['media_type'] == 'image'): ?>
                <img src="<?php echo htmlspecialchars($post['media_file']); ?>" alt="Post Image">
            <?php elseif ($post['media_type'] == 'video'): ?>
                <video controls>
                    <source src="<?php echo htmlspecialchars($post['media_file']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>

            <div class="post-actions">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" name="like_post" class="like-btn">
                        <i class="fas fa-thumbs-up"></i> 
                        <?php echo $post['user_liked'] ? 'Unlike' : 'Like'; ?>
                    </button>
                    <span><?php echo $post['like_count']; ?> Likes</span>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No posts found.</p>
<?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#follow-btn').click(function() {
        const button = $(this);
        const isFollowing = button.data('following');

        // Prepare the AJAX request data
        const action = isFollowing ? 'unfollow' : 'follow';

        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: { action: action, user_id: <?php echo $profile_user_id; ?> },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Toggle the button text and data attribute
                    button.data('following', !isFollowing);
                    button.text(isFollowing ? 'Follow' : 'Unfollow');
                } else {
                    alert(response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('An error occurred while processing your request.');
            }
        });
    });
});
</script>
</body>
</html>
