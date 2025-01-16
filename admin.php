<?php
session_start();
include 'db.php'; // Include database connection

// Redirect to login if the admin is not logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
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
            max-width: auto;
            margin: 100px 100px 100px 100px;
            background-color: rgba(223, 216, 201, 0.5);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        h2 {
            display: block;
            margin-bottom: 20px;
            color: #8c756a;
        }

        button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #8c756a;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #735c57;
        }

        .logout-btn {
            background-color: #dc3545;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .form-container {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <br/>
        <button onclick="location.href='remove_user.php'">Remove User</button>
        <button onclick="location.href='remove_post.php'">Remove Post</button>
        <button onclick="location.href='remove_comment.php'">Remove Comment</button>
        <button onclick="location.href='remove_message.php'">Remove Message</button>
        
        <!-- Logout Button -->
        <form method="POST">
            <button onclick="location.href='register.php'" type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
