<?php
session_start(); // Start the session to access user data
require 'db.php'; // Include your database connection

// Assuming you have the user ID stored in the session after login
$current_user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file -->
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-image: url('img/mountains.jpg'); /* Background image path */
    background-size: cover;
    background-attachment: fixed;
    margin: 0;
    padding: 0;
    color: #8c756a; /* Font color to match theme */
}



header {
    background-color: rgba(255, 255, 255, 0.4); /* Low opacity header */
    padding: 20px;
    text-align: center;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

header h1 {
    margin: 0;
    font-size: 2.5em;
    color: #8c756a;
}

nav a {
    margin: 0 15px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

nav a:hover {
    color: #8c756a; /* Hover effect */
}

main {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 30px;
}

/* Notification Section */
.notifications {
    background-color: rgba(255, 255, 255, 0.4); /* Transparent white */
    border-radius: 10px;
    padding: 20px;
    width: 100%;
    max-width: 600px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Notification Item */
.notification {
    padding: 15px;
    margin-bottom: 10px;
    background-color: #f8f8f8;
    border-radius: 10px;
    display: flex;
    align-items: center;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.2s ease-in-out;
}

.notification:hover {
    background-color: #e0ddd7;
}

/* Icons for Notifications */
.notification i {
    font-size: 1.5em;
    color: #8c756a;
    margin-right: 15px;
}

.notification-text {
    flex-grow: 1;
}

.notification-time {
    display: block;
    font-size: 0.8em;
    color: #999;
    text-align: right;
}

.notification:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .notifications {
        padding: 10px;
    }

    .notification {
        padding: 10px;
    }
}

</style>
</head>
<body>
    <header>
        <h1>Notifications</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="notifications">
            <?php include 'display_likes.php'; ?>
            <?php include 'display_comments.php'; ?>
            <?php include 'display_requests.php'; ?>
        </div>
    </main>
</body>
</html>

<?php
$conn->close();
?>
