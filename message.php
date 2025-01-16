<?php
session_start();
include 'db.php';  // Include your database connection file

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert message into the database
    $insert_message_query = "INSERT INTO messages (sender_id, receiver_id, message, created_at, read_status) 
                             VALUES ('$user_id', '$receiver_id', '$message', NOW(), 0)";
    mysqli_query($conn, $insert_message_query);
}

// Handle user search
$search_result = [];
if (isset($_POST['search_user'])) {
    $search_username = mysqli_real_escape_string($conn, $_POST['search_username']);
    $search_query = "SELECT * FROM users WHERE username LIKE '%$search_username%' AND id != '$user_id'";
    $search_result = mysqli_query($conn, $search_query);
}

// Fetch all conversations with other users (past chats)
$conversations_query = "SELECT DISTINCT CASE WHEN sender_id = '$user_id' THEN receiver_id ELSE sender_id END AS other_user_id
                        FROM messages
                        WHERE sender_id='$user_id' OR receiver_id='$user_id'";
$conversations_result = mysqli_query($conn, $conversations_query);

// Fetch user theme for applying dark mode styling
$user_query = "SELECT * FROM users WHERE id='$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/css/all.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .container {
            display: flex;
            width: 100%;
        }

        /* Apply dark theme color to container if dark theme is selected */
        <?php if ($user['theme'] == 'dark'): ?>
        .container {
            background-color: rgba(0, 0, 0, 0.5); /* Dark theme container color */
        }
        <?php endif; ?>

        /* Sidebar Styling */
        .sidebar {
            width: 30%;
            background-color: rgba(255, 255, 255, 0.5);
            border-right: 1px solid #ccc;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            margin: 5px;
        }

        .sidebar h2 {
            margin-bottom: 20px;
            color: #8c756a;
            font-size: 1.5em;
            text-align: center;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
            border-radius: 8px;
        }

        .chat-item:hover {
            background-color: #f1f1f1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chat-item img {
            width: 60px;
            opacity: 1;
            height: 60px; /* Ensure image is square for round shape */
            border-radius: 50%; /* Fully rounded corners */
            object-fit: cover;
        }

        .chat-item p {
            margin:20px;
            font-weight: bolder;
            text-decoration: none;
            color: black;
        }

        .time-ago {
            font-size: 0.8em;
            color: #777;
        }

        /* Search Bar Styling */
        .search-container {
            display: flex;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 300px;
            padding: 10px 20px;
            border-radius: 25px 0 0 25px;  
            border: 2px solid #8c756a;
            outline: none;
            font-size: 16px;
            color: #333;
            background-color: #dfd8c9; 
        }

        .search-container:hover {
            border-color: rgba(140, 117, 106, 0.8);
        }

        .search-container button {
            background: #8c756a;
            border: 2px solid #8c756a;
            border-radius: 0 25px 25px 0;
            padding: 10px 20px;
            font: inherit;
            cursor: pointer;
            color: #dfd8c9;
            text-decoration: none;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        /* Message Area Styling */
        .message-area {
            width: 70%;
            display: flex;
            flex-direction: column;
            padding: 20px;
            background-color: #fff;
            opacity: 0.5;
            border-radius: 10px;
        }

        .message-area h2 {
            color: #8c756a;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .messages {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: rgba(245, 245, 245, 0.8);
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
        }

        .message.sent {
            background-color: #8c756a;
            text-align: right;
            align-self: flex-end;
        }

        .message.received {
            background-color: #fff;
            align-self: flex-start;
        }

        /* Message Input and Button Styling */
        form input[type="text"] {
            width: calc(100% - 100px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px 0 0 25px;
            outline: none;
            font-size: 1em;
        }

        form button {
            padding: 10px 20px;
            background-color: #8c756a;
            color: white;
            border: none;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #8c756a;
        }

        /* Back Button Styling */
        .back-button {
            margin-bottom: 10px;
            text-decoration: none;
            background-color: #8c756a;
            opacity: 0.5;
            color: #dcf8c6;
            padding: 20px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #dcf8c6;
            color: #8c756a;
        }
    </style>
</head>
<body>
    <a href="home.php" class="back-button"><i class="fas fa-home"></i></a>

    <div class="container">
        <div class="sidebar">
            <h2>Chats</h2>
            <div class="search-container">
                <form method="POST" action="">
                    <input type="text" name="search_username" placeholder="Search user..." required>
                    <button type="submit" name="search_user"><i class="fa fa-search" aria-hidden="true"></i></button>
                </form>
            </div>
            
            <?php if (isset($_POST['search_user'])): ?>
                <?php if (mysqli_num_rows($search_result) > 0): ?>
                    <?php while ($user = mysqli_fetch_assoc($search_result)): ?>
                        <div class="chat-item">
                            <a href="?chat_with=<?php echo $user['id']; ?>">
                                <img src="<?php echo $user['profile_pic'] ?: 'default.jpg'; ?>" alt="Profile Picture">
                                <p><?php echo $user['name']; ?></p>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-result">No users found!</p>
                <?php endif; ?>
            <?php else: ?>
                <h3>Recent Conversations</h3>
                <?php while ($conversation = mysqli_fetch_assoc($conversations_result)): ?>
                    <?php 
                    $other_user_id = $conversation['other_user_id'];
                    $other_user_query = "SELECT * FROM users WHERE id='$other_user_id'";
                    $other_user_result = mysqli_query($conn, $other_user_query);
                    $other_user = mysqli_fetch_assoc($other_user_result);
                    ?>
                    <div class="chat-item">
                        <a href="?chat_with=<?php echo $other_user['id']; ?>">
                            <img src="<?php echo $other_user['profile_pic'] ?: 'default.jpg'; ?>" alt="Profile Picture">
                            <p><?php echo $other_user['name']; ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="message-area">
            <h2>Chat</h2>
            <div class="messages">
                <?php
                if (isset($_GET['chat_with'])) {
                    $chat_with = $_GET['chat_with'];

                    // Fetch messages
                    $messages_query = "SELECT * FROM messages WHERE (sender_id='$user_id' AND receiver_id='$chat_with') 
                                       OR (sender_id='$chat_with' AND receiver_id='$user_id') ORDER BY created_at ASC";
                    $messages_result = mysqli_query($conn, $messages_query);

                    while ($message = mysqli_fetch_assoc($messages_result)) {
                        $is_sent = ($message['sender_id'] == $user_id) ? 'sent' : 'received';
                        echo "<div class='message $is_sent'>{$message['message']}</div>";
                    }
                }
                ?>
            </div>
            
            <?php if (isset($_GET['chat_with'])): ?>
                <form method="POST">
                    <input type="hidden" name="receiver_id" value="<?php echo $_GET['chat_with']; ?>">
                    <input type="text" name="message" placeholder="Type your message here..." required>
                    <button type="submit" name="send_message"><i class="fas fa-paper-plane"></i></button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
