<?php
session_start();
require 'db.php';

$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Fetch following
$following_query = "SELECT u.id, u.username, u.bio FROM users u 
                    JOIN friend_requests fr ON u.id = fr.receiver_id 
                    WHERE fr.sender_id = ? AND fr.status = 'accepted'";
$stmt_following = $conn->prepare($following_query);
$stmt_following->bind_param("i", $profile_user_id);
$stmt_following->execute();
$following_result = $stmt_following->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Following</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Following</h1>
    <a href="profile.php?id=<?php echo $profile_user_id; ?>">Back to Profile</a>
    <ul>
        <?php while ($following = $following_result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($following['username']); ?> - <?php echo htmlspecialchars($following['bio']); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php
$stmt_following->close();
$conn->close();
?>
