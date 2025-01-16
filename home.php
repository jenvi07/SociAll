<?php
session_start();
require 'db.php'; // Include your database connection
include('ajax.php');

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch logged-in user details
$user_query = "SELECT * FROM users WHERE id='$user_id'";
$user_result = mysqli_query($conn, $user_query);
if (!$user_result) {
    die("Error fetching user details: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_result);

// Fetch all posts with user details and like counts
$post_query = "
    SELECT post.*, users.name, users.username, users.profile_pic, users.visibility,
        (SELECT COUNT(*) FROM likes WHERE post_id = post.id) AS like_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = post.id AND user_id = '$user_id') AS user_liked
    FROM post 
    JOIN users ON post.user_id = users.id 
    ORDER BY post.created_at DESC";
$post_result = mysqli_query($conn, $post_query);
if (!$post_result) {
    die("Error fetching posts: " . mysqli_error($conn));
}

// Handle user search
$search_results = [];
if (isset($_POST['search_user'])) {
    $search_username = mysqli_real_escape_string($conn, $_POST['search_username']);
    $search_query = "
        SELECT id, name, username, profile_pic 
        FROM users 
        WHERE username LIKE '%$search_username%' 
        OR name LIKE '%$search_username%'";
    $search_result = mysqli_query($conn, $search_query);
    if ($search_result && mysqli_num_rows($search_result) > 0) {
        $search_results = mysqli_fetch_all($search_result, MYSQLI_ASSOC);
    }
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    if (isset($_POST['post_id']) && !empty($_POST['comment_text'])) {
        $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);
        $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
        $user_id = $_SESSION['user_id'];

        $query = "INSERT INTO comments (post_id, user_id, comment_text) 
                  VALUES ('$post_id', '$user_id', '$comment_text')";
        if (mysqli_query($conn, $query)) {
            header("Location: home.php?message=Comment added successfully.");
            exit();
        } else {
            die("Error inserting comment: " . mysqli_error($conn));
        }
    }
}

// Handle like functionality
if (isset($_POST['like_post'])) {
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']);

    // Check if the user has already liked the post
    $like_check_query = "SELECT * FROM likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
    $like_check_result = mysqli_query($conn, $like_check_query);

    if (mysqli_num_rows($like_check_result) == 0) {
        // If not liked, insert a like
        $like_query = "INSERT INTO likes (post_id, user_id) VALUES ('$post_id', '$user_id')";
        mysqli_query($conn, $like_query);
    } else {
        // If already liked, remove the like
        $unlike_query = "DELETE FROM likes WHERE post_id = '$post_id' AND user_id = '$user_id'";
        mysqli_query($conn, $unlike_query);
    }
    header("Location: home.php");
    exit();
}

// Fetch friends where status is approved
$friends_query = "
    SELECT 
        CASE 
            WHEN sender_id = '$user_id' THEN receiver_id
            ELSE sender_id 
        END AS friend_id 
    FROM friend_requests 
    WHERE (sender_id = '$user_id' OR receiver_id = '$user_id') 
      AND status = 1
";

$result = mysqli_query($conn, $friends_query);
if (!$result) {
    die("Error fetching friends: " . mysqli_error($conn));
}

$friends = [];
while ($row = mysqli_fetch_assoc($result)) {
    $friends[] = $row['friend_id'];
}
//converting time into human readable form
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks from days
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7; // Remaining days after weeks

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );

    // Create an array with the correct values
    $timeStrings = [];
    if ($diff->y) {
        $timeStrings[] = $diff->y . ' ' . $string['y'] . ($diff->y > 1 ? 's' : '');
    }
    if ($diff->m) {
        $timeStrings[] = $diff->m . ' ' . $string['m'] . ($diff->m > 1 ? 's' : '');
    }
    if ($weeks) {
        $timeStrings[] = $weeks . ' ' . $string['w'] . ($weeks > 1 ? 's' : '');
    }
    if ($days) {
        $timeStrings[] = $days . ' ' . $string['d'] . ($days > 1 ? 's' : '');
    }
    if ($diff->h) {
        $timeStrings[] = $diff->h . ' ' . $string['h'] . ($diff->h > 1 ? 's' : '');
    }
    if ($diff->i) {
        $timeStrings[] = $diff->i . ' ' . $string['i'] . ($diff->i > 1 ? 's' : '');
    }
    if ($diff->s) {
        $timeStrings[] = $diff->s . ' ' . $string['s'] . ($diff->s > 1 ? 's' : '');
    }

    // Only return the first element if not full
    if (!$full) $timeStrings = array_slice($timeStrings, 0, 1);

    return $timeStrings ? implode(', ', $timeStrings) . ' ago' : 'just now';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SociAll - Dashboard</title>
    <link rel="stylesheet" href="styles.css"/>
    <link rel="stylesheet" href="css/css/all.css"/>
    <script src="js/js/all.js" crossorigin="anonymous"></script>
    <script src="js/sociAll.js"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script>
        function toggleCommentForm(postId) {
            const commentSection = document.getElementById('comment-section-' + postId);
            if (commentSection.style.display === 'none' || commentSection.style.display === '') {
                commentSection.style.display = 'block';
            } else {
                commentSection.style.display = 'none';
            }
        }

        function sharePost(postId) {
            alert('Share button clicked for post ID: ' + postId); // Debugging
            document.getElementById('share-form-' + postId).submit();
        }

        document.querySelector('.file-upload-button').addEventListener('click', function() {
        document.getElementById('file').click(); // Trigger file input when button is clicked
});

// Like/Unlike functionality
$('.like-btn').on('click', function() {
    const postId = $(this).data('post-id');
    const button = $(this);

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: { action: 'like_post', post_id: postId },
        success: function(response) {
            const result = JSON.parse(response);
            const likeCount = button.find('.like-count');
            const currentCount = parseInt(likeCount.text(), 10);
            likeCount.text(result.liked ? currentCount + 1 : currentCount - 1);
            button.find('i').toggleClass('fa-thumbs-up fa-thumbs-down');
        }
    });
});

// Check for new notifications
function checkNotifications() {
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: { action: 'check_notifications' },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.new_notifications > 0) {
                $('.notification-icon').addClass('new-notification'); // Add red dot or highlight
                $('.notification-icon').text(result.new_notifications); // Show the number
            } else {
                $('.notification-icon').removeClass('new-notification');
            }
        }
    });
}

// Check for new messages
function checkMessages() {
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: { action: 'check_messages' },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.new_messages > 0) {
                $('.message-icon').addClass('new-message'); // Add red dot or highlight
                $('.message-icon').text(result.new_messages); // Show the number
            } else {
                $('.message-icon').removeClass('new-message');
            }
        }
    });
}

// Call this function periodically to check for messages
setInterval(checkMessages, 2000); // Every 2 seconds
setInterval(checkNotifications, 5000); // Every 5 seconds

    </script>
    <style>
        /* Apply dark theme color to container if dark theme is selected */
        <?php if ($user['theme'] == 'dark'): ?>
        .container {
            background-color: rgba(0, 0, 0, 0.5); /* Dark theme container color */
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <!-- Header -->
<div class="header">
    <header class="header-content">
        <!-- Logo on the left side -->
        <div class="logo">
            <img src="img/logo.png" alt="Logo" class="logo-img"> <!-- Update the path to your logo -->
        </div>

        <!-- Center Title -->
        <h1 class="site-title">SociAll</h1>

        <!-- Profile Picture on the right side -->
        <div class="profile-menu">
            <a href="profile.php?user_id=<?php echo $user_id; ?>">
                <div class="profile-pic">
                    <img src="<?php echo $user['profile_pic'] ?: 'default.jpg'; ?>" alt="Profile Picture">
                </div>
            </a>
        </div>
    </header>
</div>


    <!-- Main container -->
    <div class="container">
        <!-- Sidebar Navigation -->
        <nav class="navbar">
            <ul>
                <li><a href="home.php"><i class="fas fa-home"></i></a></li><br/>
                <li><a href="profile.php?user_id=<?php echo $user_id; ?>"><i class="fas fa-user"></i></a></li><br/>
                <li><a href="message.php"><i class="fas fa-envelope"></i></a></li><br/>
                <li><a href="notification.php"><i class="fa fa-bell" aria-hidden="true"></i></a></li><br/>
                <li><a href="account_settings.php"><i class="fas fa-cog"></i></a></li><br/>
            </ul>
        </nav>

        <!-- Feed Section -->
        <section class="feed">
            <div class="post-form">
                <h2>Create a Post</h2>
                <form action="upload_posts.php" method="post" enctype="multipart/form-data">
                    <textarea name="text_content" id="text_content" rows="4" placeholder="What's on your mind?"></textarea>
                    <div class="file-upload">
                    <input type="file" name="media" id="file" accept="image/*,video/*">
                    <label for="file" class="file-upload-button"><i class="fas fa-camera"></i></label>                    
                    </div>
                    <button type="submit" name="submit" class="file-upload-button"><i class="fa fa-upload" aria-hidden="true"></i></button>
                </form>
            </div>

            <h1>Feed</h1>
            <?php while ($post = mysqli_fetch_assoc($post_result)): ?>
    <div class="post">
        <div class="user-info-post">
            <div class="profile-menu">
                <a href="display_profile.php?user_id=<?php echo $post['user_id']; ?>">
                    <div class="profile-pic">
                        <img src="<?php echo htmlspecialchars($post['profile_pic']); ?>" alt="Profile Picture">
                    </div>
                </a>
            </div>
            <div class="user-details">
                <strong><?php echo htmlspecialchars($post['name']); ?></strong><br>
                <small>@<?php echo htmlspecialchars($post['username']); ?></small>
            </div>
        </div>

        <!-- Media (image/video) -->
        <?php if ($post['media_type'] == 'image'): ?>
            <img src="<?php echo htmlspecialchars($post['media_file']); ?>" alt="Post Image" class="post-media">
        <?php elseif ($post['media_type'] == 'video'): ?>
            <video controls class="post-media">
                <source src="<?php echo htmlspecialchars($post['media_file']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        <?php endif; ?>

        <p><?php echo nl2br(htmlspecialchars($post['text_content'])); ?></p>

        <!-- Add this line to display time -->
        <p class="post-time"><?php echo time_elapsed_string($post['created_at']); ?></p>

        <div class="post-actions">
            <form method="POST" style="display:inline;">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" name="like_post" class="like-btn" data-post-id="<?php echo $post['id']; ?>">
                    <i class="fas <?php echo $post['user_liked'] ? 'fa-thumbs-down' : 'fa-thumbs-up'; ?>"></i>
                </button>
                <span class="like-count"><?php echo $post['like_count']; ?></span>
            </form>

            <button onclick="toggleCommentForm(<?php echo $post['id']; ?>)" class="comment-btn"><i class="fa-solid fa-comment"></i></button>
        </div>

        <div id="comment-section-<?php echo $post['id']; ?>" class="comment-section" style="display:none;">
            <h3>Comments:</h3>
            <form method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <textarea name="comment_text" placeholder="Add a comment..." required></textarea>
                <button type="submit" name="submit_comment"><i class="fa-regular fa-comment"></i></button>
            </form>

            <?php
            // Fetch comments for the post
            $comment_query = "SELECT comments.*, users.name AS commenter_name 
                              FROM comments 
                              JOIN users ON comments.user_id = users.id 
                              WHERE comments.post_id = '{$post['id']}' 
                              ORDER BY comments.created_at DESC";
            $comment_result = mysqli_query($conn, $comment_query);
            while ($comment = mysqli_fetch_assoc($comment_result)): ?>
                <div class="comment">
                    <strong><?php echo htmlspecialchars($comment['commenter_name']); ?>:</strong>
                    <p><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php endwhile; ?>

</section>

        <!-- Sidebar for user search and friend suggestions -->
        <aside class="sidebar">
            <div class="user-search">
                <h2>Search Users</h2>
                <form method="POST">
                    <div class="search-bar">
                    <input type="search" name="search_username" placeholder="Search by username" required>            
                    <button type="submit" class="link-button" name="search_user"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
                <ul>
                    <?php foreach ($search_results as $result): ?>
                        <li>
                            <div class="user-box">
                            <img src="<?php echo $result['profile_pic'] ?: 'default.jpg'; ?>" alt="Profile Picture" class="profile-pic">
                            <div class="user-info">
                            <a href="display_profile.php?user_id=<?php echo $result['id']; ?>"><?php echo htmlspecialchars($result['name']); ?></a>
                            <p>(@<?php echo htmlspecialchars($result['username']); ?>)</p>
                            </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Include users.php file here for additional suggestions -->
            <div class="suggestions">        
                <?php include 'users.php'; // Include the user suggestions here ?>
            </div>
        </aside>
    </div>
</body>
</html>
