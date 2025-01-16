<?php
// Start session and include database connection
session_start();
require 'db.php';

// Get profile user ID from URL or use the logged-in user's ID if not provided
$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];
$logged_in_user_id = $_SESSION['user_id'];

// Fetch user information based on profile ID
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $profile_user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

// Fetch counts for followers, following, and friends
$follower_query = "SELECT COUNT(*) as follower_count FROM friend_requests WHERE receiver_id = ? AND status = 'accepted'";
$stmt_follower = $conn->prepare($follower_query);
$stmt_follower->bind_param("i", $profile_user_id);
$stmt_follower->execute();
$follower_result = $stmt_follower->get_result()->fetch_assoc();

$following_query = "SELECT COUNT(*) as following_count FROM friend_requests WHERE sender_id = ? AND status = 'accepted'";
$stmt_following = $conn->prepare($following_query);
$stmt_following->bind_param("i", $profile_user_id);
$stmt_following->execute();
$following_result = $stmt_following->get_result()->fetch_assoc();

$friends_query = "SELECT COUNT(*) as friend_count FROM friend_requests WHERE (sender_id = ? OR receiver_id = ?) AND status = 'accepted'";
$stmt_friends = $conn->prepare($friends_query);
$stmt_friends->bind_param("ii", $profile_user_id, $profile_user_id);
$stmt_friends->execute();
$friends_result = $stmt_friends->get_result()->fetch_assoc();

// Check if the logged-in user is viewing their own profile
$is_own_profile = ($profile_user_id === $logged_in_user_id);

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
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/css/all.css">
    <script src="js/js/all.js" crossorigin="anonymous"></script>
    <script src="js/sociAll.js"></script>
    <style>
        /* Profile page styles */
    body {
    font-family: Arial, sans-serif;
    background-image: url('img/mountains.jpg');
    background-size: cover;
    margin: 0; /* Ensure there is no margin */
    padding: 0; /* Ensure there is no padding */
    height: 100vh; /* Full viewport height */
    overflow: auto; /* Allow scrolling */
}

.profile-container {
    margin: 20px auto;
    padding: 20px;
    width: 90%;
    max-width: 800px;
    background-color: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-height: 800px; /* Limit the height of the profile container */
    overflow-y: auto; /* Enable vertical scrolling */
}

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: #8c756a;
            opacity: 0.7;
            padding: 20px;
            border-radius: 10px;
        }
        .profile-pic {
            border-radius: 50%;
            height: 150px;
            width: 150px;
            object-fit: cover;
            margin-right: 20px;
        }
        .profile-info {
            flex: 1;
            color: #dfd8c9;
        }
        .profile-info h2 {
            margin: 0;
            color: #dfd8c9;
        }
        .profile-info p.bio {
            font-style: italic;
            color: #dfd8c9;
        }
        .profile-info .stats {
            display: flex;
            gap: 30px;
            margin-top: 10px;
        }
        .profile-info .stat-item {
            text-align: center;
            color: #dfd8c9;
        }
        .profile-info .stat-item a {
            text-decoration: none;
            color: #dfd8c9;
        }
        .profile-info .stat-item a:hover {
            text-decoration: underline;
        }
        .profile-info .stat-item strong {
            display: block;
            font-size: 18px;
            font-weight: bold;
        }
        .edit-profile-btn {
            margin-top: 10px;
        }
        .edit-profile-btn a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dfd8c9;
            color: #8c756a;
            text-decoration: none;
            border-radius: 5px;
        }
        .edit-profile-btn a:hover {
            background-color: #e8e0d1;
        }
        .posts-container {
            margin-top: 20px;
        }
        .header-nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #8c756a;
        }
        .header-nav-container h1 {
            color: #dfd8c9;
            margin-right: auto;
        }
        nav a {
            margin-right: 20px;
            color: #dfd8c9;
            text-decoration: none;
            font: bold;
            font-size: 1cm;
        }
        nav a:hover {
            text-decoration: none;
            color: black;
            font-weight: bolder;
        }

        /* Media styles */
.post-media {
    width: 100%; /* Full width */
    height: auto; /* Maintain aspect ratio */
    max-height: 300px; /* Limit the height */
    object-fit: cover; /* Ensure the content fits */
    margin-top: 10px; /* Spacing above the media */
}

/* Post actions styles */
.post {
    background-color: #f9f9f9; /* Light background for posts */
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.post-time{
    display: block;
}
.post-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.like-button, .comment-button {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    background-color: #8c756a; /* Button color */
    color: #dfd8c9; /* Text color */
    cursor: pointer;
    margin-right: 10px;
    
}

.like-button:hover, .comment-button:hover {
    background-color: #dfd8c9; /* Hover color */
    color: #8c756a; /* Hover text color */
}
/* Responsive styles */
@media (max-width: 600px) {
    .profile-container {
        width: 95%; /* Full width on small screens */
    }

    .profile-pic {
        height: 100px; /* Smaller profile picture */
        width: 100px;
    }

    .profile-info h2 {
        font-size: 1.5em; /* Responsive font size */
    }

    .stats {
        flex-direction: column; /* Stack stats vertically */
        gap: 10px; /* Space between stats */
    }

    .post {
        padding: 10px; /* Less padding on small screens */
    }
}

    </style>
</head>
<body>
    <!-- Merged Header and Navbar -->
    <div class="header-nav-container">
        <h1>SociAll</h1>
        <nav>
            <a href="home.php"><i class="fa-solid fa-house"></i></a>
            <a href="notification.php?id=<?php echo $_SESSION['user_id']; ?>"><i class="fa-solid fa-bell"></i></a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a>
        </nav>
    </div>

    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="profile-pic">
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="bio"><?php echo htmlspecialchars($user['bio']); ?></p>

                <!-- Followers, Following, and Friends Counts -->
                <div class="stats">
                    <div class="stat-item">
                        <a href="followers.php?id=<?php echo $profile_user_id; ?>">
                            <strong><?php echo $follower_result['follower_count']; ?></strong>Followers
                        </a>
                    </div>
                    <div class="stat-item">
                        <a href="following.php?id=<?php echo $profile_user_id; ?>">
                            <strong><?php echo $following_result['following_count']; ?></strong>Following
                        </a>
                    </div>
                    <div class="stat-item">
                        <a href="friends.php?id=<?php echo $profile_user_id; ?>">
                            <strong><?php echo $friends_result['friend_count']; ?></strong>Friends
                        </a>
                    </div>
                </div>

                <!-- Show Edit Profile button if it's the logged-in user's profile -->
                <?php if ($is_own_profile): ?>
                    <div class="edit-profile-btn">
                        <a href="edit_profile.php">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Posts Section -->
<div class="posts-container">
    <h3>Posts</h3>
    <?php
    // Fetch and display user posts
    $post_query = "SELECT * FROM post WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_posts = $conn->prepare($post_query);
    $stmt_posts->bind_param("i", $profile_user_id);
    $stmt_posts->execute();
    $posts_result = $stmt_posts->get_result();

    if ($posts_result->num_rows > 0) {
        while ($post = $posts_result->fetch_assoc()) {
            echo "<div class='post'>";
            echo "<p>" . htmlspecialchars($post['text_content']) . "</p>";

            // Display media if available
            if (!empty($post['media_file'])) {
                if ($post['media_type'] === 'image') {
                    echo "<img src='" . htmlspecialchars($post['media_file']) . "' alt='Post Image' class='post-media'>";
                } elseif ($post['media_type'] === 'video') {
                    echo "<video controls class='post-media'>";
                    echo "<source src='" . htmlspecialchars($post['media_file']) . "' type='video/mp4'>";
                    echo "Your browser does not support the video tag.";
                    echo "</video>";
                }
            }

            // Show time ago
            echo "<small class='post-time'>Posted " . time_elapsed_string($post['created_at']) . "</small>";

            // Like and comment buttons
            echo "<div class='post-actions'>";
            echo "<button class='like-button'>Like<i class="."fa-solid fa-thumbs-up"."></i></button>";
            echo "<button class='comment-button'>Comment<i class="."fa-solid fa-comment"."></i></button>";
            echo "</div>";

            echo "</div>"; // Closing .post
        }
    } else {
        echo "<p>No posts yet.</p>";
    }
    ?>
</div>

    </div>

    <!-- Close database connections -->
    <?php
    $stmt_user->close();
    $stmt_follower->close();
    $stmt_following->close();
    $stmt_friends->close();
    $conn->close();
    ?>
</body>
</html>
