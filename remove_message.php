<?php
session_start();
include 'db.php'; // Include your database connection file

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = mysqli_real_escape_string($conn, $_POST['message_id']);

    // Query to delete message
    $delete_query = "DELETE FROM messages WHERE id='$message_id'";
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Message removed successfully.";
    } else {
        $error_message = "Failed to remove the message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Message</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/css/all.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-image: url('img/mountains.jpg');
            background-size: cover;
            background-position: center;
        }

        .container {
            max-width: 500px;
            margin: 100px auto;
            background-color: rgba(223, 216, 201, 0.5);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        h2 {
            color: #8c756a;
        }

        input[type="text"],
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #8c756a;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #735c57;
        }

        .message {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Remove Message</h2>

        <?php if (isset($success_message)) { echo "<p class='message'>$success_message</p>"; } ?>
        <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

        <form method="POST">
            <label for="message_id">Message ID:</label>
            <input type="text" id="message_id" name="message_id" required>

            <button type="submit">Remove Message</button>
        </form>
    </div>
</body>
</html>
