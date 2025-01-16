<?php
require_once 'db.php';  // No session_start() here, as session is handled in home page

$logged_in_user_id = $_SESSION['user_id'];  // Assuming session is set from home page

// Fetch users, sorted by the follow status (unfollowed first, then pending, then followed)
$query = "
    SELECT u.id, u.name, u.username, u.profile_pic, 
    COALESCE(fr.status, 'not_friends') AS friendship_status
    FROM users u
    LEFT JOIN friend_requests fr 
    ON (fr.sender_id = ? AND fr.receiver_id = u.id) 
    OR (fr.sender_id = u.id AND fr.receiver_id = ?)
    WHERE u.id != ?
    GROUP BY u.id
    ORDER BY 
        CASE 
            WHEN fr.status = 'accepted' THEN 2  -- Followed users at the bottom
            WHEN fr.status = 'pending' THEN 1   -- Pending requests in the middle
            ELSE 0                             -- Unfollowed users at the top
        END ASC, u.id ASC";  // Sort by status and then by user ID for consistency

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $logged_in_user_id, $logged_in_user_id, $logged_in_user_id);
$stmt->execute();
$suggestions_result = $stmt->get_result();

echo "<div class='user-suggestions'>";
echo "<h2>User Suggestions</h2>";
echo "<div class='suggestions-container'>";

// Loop through and display each user suggestion
if ($suggestions_result && $suggestions_result->num_rows > 0) {
    while ($suggestion = $suggestions_result->fetch_assoc()) {
        echo "<div class='suggestion-card'>";

        // User profile picture (centered)
        echo "<div class='profile-pic-container'>";
        echo "<a href='display_profile.php?user_id=" . htmlspecialchars($suggestion['id']) . "'>";
        echo "<img src='" . htmlspecialchars($suggestion['profile_pic'] ?: 'default.jpg') . "' alt='Profile Picture' class='profile-pic'>";
        echo "</a></div>";

        // User info (centered below profile pic)
        echo "<div class='user-info'>";
        echo "<a href='display_profile.php?user_id=" . htmlspecialchars($suggestion['id']) . "'>";
        echo "<p class='user-name'>" . htmlspecialchars($suggestion['name']) . "</p>";
        echo "</a>";
        echo "<p class='user-username'>@" . htmlspecialchars($suggestion['username']) . "</p>";
        echo "</div>";

        // Follow/unfollow button logic (centered below username)
        $friendship_status = $suggestion['friendship_status'];

        echo "<div class='action-button'>";
        if ($friendship_status === 'accepted') {
            // Show 'Unfollow' button if already following
            echo "<form method='POST' action='follow.php'>
                    <input type='hidden' name='receiver_id' value='" . htmlspecialchars($suggestion['id']) . "'>
                    <input type='hidden' name='action' value='unfollow'>
                    <button type='submit' class='unfollow-button'>Unfollow</button>
                  </form>";
        } elseif ($friendship_status === 'pending') {
            // Show 'Request Sent' button for pending requests
            echo "<form method='POST' action='follow.php'>
                    <input type='hidden' name='receiver_id' value='" . htmlspecialchars($suggestion['id']) . "'>
                    <input type='hidden' name='action' value='unfollow'>
                    <button type='submit' class='follow-button pending'>Request Sent</button>
                  </form>";
        } else {
            // Show 'Follow' button for users not followed
            echo "<form method='POST' action='follow.php'>
                    <input type='hidden' name='receiver_id' value='" . htmlspecialchars($suggestion['id']) . "'>
                    <input type='hidden' name='action' value='follow'>
                    <button type='submit' class='follow-button'>Follow</button>
                  </form>";
        }
        echo "</div>"; // End action-button div

        echo "</div>"; // End suggestion-card div
    }
} else {
    echo "<p>No more user suggestions to display.</p>";
}

echo "</div></div>"; // End suggestions-container and user-suggestions div

$stmt->close();
$conn->close();
?>

<!-- CSS Styling -->
<style>
.user-suggestions {
    width: 80%;
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f8f8;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.suggestions-container {
    display: flex;
    flex-direction: column;  /* Display suggestions in a single column (one per row) */
    gap: 20px;
    overflow-y: auto; /* Enable scrolling */
    height: 7cm; /* Adjust height similarly */
    background-color: white;
    padding: 0%;
}

.suggestion-card {
    background-color: white;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;  /* Ensure the suggestion card doesn't stretch */
    margin: 0 auto;  /* Center it horizontally */
}

.profile-pic-container {
    margin-bottom: 15px;
}

.profile-pic {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info {
    margin-bottom: 15px;
    color: black;
}

.user-name {
    font-size: 16px;
    color: black;
    font-weight: bold;
    margin: 0;
}

.user-username {
    color: black;
    margin: 5px 0 0;
}

.action-button form {
    width: 100%;
}

.follow-button, .unfollow-button {
    padding: 8px 12px;
    background-color: #8c756a;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 100%;
}

.follow-button:hover, .unfollow-button:hover {
    background-color: #6b5a49;
}

.follow-button.pending {
    background-color: #8c756a;
    cursor: not-allowed;
}
</style>
