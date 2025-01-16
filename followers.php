<?php
session_start();
require 'db.php';

$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Fetch followers
$follower_query = "SELECT u.id, u.username, u.bio FROM users u 
                   JOIN friend_requests fr ON u.id = fr.sender_id 
                   WHERE fr.receiver_id = ? AND fr.status = 'accepted'";
$stmt_followers = $conn->prepare($follower_query);
$stmt_followers->bind_param("i", $profile_user_id);
$stmt_followers->execute();
$followers_result = $stmt_followers->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Followers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Followers</h1>
    <a href="profile.php?id=<?php echo $profile_user_id; ?>">Back to Profile</a>
    <ul>
        <?php while ($follower = $followers_result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($follower['username']); ?> - <?php echo htmlspecialchars($follower['bio']); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php
$stmt_followers->close();
$conn->close();
?>
