<?php
session_start();
require 'db.php';

$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Fetch friends (either sender or receiver of friend requests)
$friends_query = "SELECT u.id, u.username, u.bio FROM users u 
                  JOIN friend_requests fr ON (u.id = fr.sender_id OR u.id = fr.receiver_id) 
                  WHERE (fr.sender_id = ? OR fr.receiver_id = ?) AND fr.status = 'accepted'";
$stmt_friends = $conn->prepare($friends_query);
$stmt_friends->bind_param("ii", $profile_user_id, $profile_user_id);
$stmt_friends->execute();
$friends_result = $stmt_friends->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Friends</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Friends</h1>
    <a href="profile.php?id=<?php echo $profile_user_id; ?>">Back to Profile</a>
    <ul>
        <?php while ($friend = $friends_result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($friend['username']); ?> - <?php echo htmlspecialchars($friend['name']); ?></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php
$stmt_friends->close();
$conn->close();
?>
