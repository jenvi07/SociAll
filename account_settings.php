<?php
session_start();
include 'db.php'; // Include your database connection file

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user settings from the database
$user_query = "SELECT * FROM users WHERE id='$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Update user settings if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['theme']) && isset($_POST['visibility'])) {
        $theme = mysqli_real_escape_string($conn, $_POST['theme']);
        $visibility = mysqli_real_escape_string($conn, $_POST['visibility']);
        
        // Update theme and visibility in the database
        $update_query = "UPDATE users SET theme='$theme', visibility='$visibility' WHERE id='$user_id'";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Settings updated successfully!";
            // Reload user settings after update
            $user['theme'] = $theme;
            $user['visibility'] = $visibility;
        } else {
            $_SESSION['error'] = "Failed to update settings.";
        }
    }

    // Logout functionality
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="css/css/all.css">
    <link rel="stylesheet" href="styles.css">
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
            max-width: 600px; /* Adjusted max-width */
            margin: 100px auto; /* Center the container */
            background-color: rgba(223, 216, 201, 0.5); /* Light theme container color */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Apply dark theme color to container if dark theme is selected */
        <?php if ($user['theme'] == 'dark'): ?>
        .container {
            background-color: rgba(0, 0, 0, 0.5); /* Dark theme container color */
            color: dfd8c9;
        }
        <?php endif; ?>

        h2 {
            margin-bottom: 20px;
            color: #8c756a; /* Font color */
        }

        form {
            display: flex;
            flex-direction: column; /* Stack elements vertically */
        }

        label {
            margin: 10px 0 5px;
            color: #8c756a; /* Font color */
        }

        select,
        button {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #8c756a; /* Button background color */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #735c57; /* Darker shade for hover effect */
        }

        .logout-btn {
            background-color: #dc3545; /* Logout button color */
        }

        .logout-btn:hover {
            background-color: #c82333; /* Logout button hover color */
        }

        .message {
            margin: 10px 0;
            color: #8c756a;
        }

        .error {
            margin: 10px 0;
            color: #8c756a;
        }

        .back-button {
            display: block;
            margin-bottom: 20px;
            text-decoration: none;
            background-color: #8c756a; /* Back button color */
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #735c57; /* Back button hover color */
        }

        .options {
            display: flex; /* Ensure options are flex items */
            flex-direction: column; /* Stack options vertically */
            margin-bottom: 20px; /* Margin to separate options */
        }

        .options select {
            border: 3px solid #8c756a; /* Border color */
            color: #8c756a; /* Font color */
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="home.php" class="back-button"><i class="fas fa-home"></i></a>
        <h2>Account Settings</h2>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="options">
                <!-- Theme Selection -->
                <label for="theme">Select Theme:</label>
                <select name="theme" id="theme">
                    <option value="light" <?php if ($user['theme'] == 'light') echo 'selected'; ?>>Light</option>
                    <option value="dark" <?php if ($user['theme'] == 'dark') echo 'selected'; ?>>Dark</option>
                </select>

                <!-- Profile Visibility -->
                <label for="visibility">Profile Visibility:</label>
                <select name="visibility" id="visibility">
                    <option value="public" <?php if ($user['visibility'] == 'public') echo 'selected'; ?>>Public</option>
                    <option value="private" <?php if ($user['visibility'] == 'private') echo 'selected'; ?>>Private</option>
                </select>
            </div>

            <!-- Save Settings Button -->
            <button type="submit">Save Settings</button>

            <!-- Logout Button -->
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
