<?php
session_start();
require 'db.php'; // Include the database connection

// Assuming the user is logged in and their user ID is stored in session
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $text_content = $_POST['text_content'] ?? '';
    $media_type = '';
    $media_file = '';

    // Handle file upload if a file is provided
    if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['media'];
        $file_name = basename($file['name']);
        $target_dir = 'uploads/';
        $target_file = $target_dir . time() . "_" . $file_name; // Unique file name using timestamp
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Ensure the uploads directory exists and is writable
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Check if it's an image or a video
        if (in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $media_type = 'image';
        } elseif (in_array($file_type, ['mp4', 'avi', 'mov', 'mkv'])) {
            $media_type = 'video';
        } else {
            echo "Invalid file type!";
            exit;
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $media_file = $target_file;
        } else {
            echo "Failed to upload file.";
            exit;
        }
    } else {
        $media_type = 'text'; // If no file is uploaded, treat the post as a text post
    }

    // Insert the post into the database
    $stmt = $conn->prepare("INSERT INTO post (user_id, text_content, media_file, media_type) VALUES (?, ?, ?, ?)");
    
    // Check if the prepare() function was successful
    if ($stmt === false) {
        die("Error in prepare statement: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param('isss', $user_id, $text_content, $media_file, $media_type);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "Post uploaded successfully!";
        header("Location: home.php"); // Redirect to the dashboard or feed page
        exit;
    } else {
        echo "Failed to upload post!";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

