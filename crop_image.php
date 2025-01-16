<?php
session_start();
require 'db.php'; // Database connection

$user_id = $_SESSION['user_id']; // Get the logged-in user ID

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image'])) {
        $image_file = $_FILES['image']['tmp_name'];

        // Generate a unique name for the image file
        $file_name = 'profile_' . $user_id . '.png';
        $file_path = 'img/profile_pictures/' . $file_name;

        // Move the uploaded file to the server
        move_uploaded_file($image_file, $file_path);

        // Update the database with the new image path
        $update_query = "UPDATE users SET profile_pic='$file_name' WHERE id='$user_id'";
        mysqli_query($conn, $update_query);

        // Redirect back to edit_profile.php
        header('Location: edit_profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crop Image</title>
    <link rel="stylesheet" href="css/cropper.css"> <!-- Include Cropper CSS -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            text-align: center;
        }
        #crop-image {
            max-width: 100%;
        }
        button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #8c756a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>

<h1>Select and Crop Your Profile Picture</h1>

<!-- File input for image upload -->
<input type="file" id="image-input" accept="image/*">

<!-- Button to trigger file input -->
<button id="upload-btn">Select Image</button>

<!-- Crop Image Preview -->
<img id="crop-image" style="display: none;" alt="Image to crop">

<!-- Save button to confirm cropping -->
<button id="save-btn" style="display: none;">Save Cropped Image</button>

<script src="js/cropper.js"></script>
<script>
    const imageInput = document.getElementById('image-input');
    const cropImage = document.getElementById('crop-image');
    const saveBtn = document.getElementById('save-btn');
    const uploadBtn = document.getElementById('upload-btn');

    let cropper;

    uploadBtn.addEventListener('click', () => {
        imageInput.click(); // Trigger file input
    });

    imageInput.addEventListener('change', () => {
        const file = imageInput.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            cropImage.src = e.target.result;
            cropImage.style.display = 'block'; // Show the image
            saveBtn.style.display = 'block'; // Show the save button

            // Initialize Cropper.js on the image
            cropper = new Cropper(cropImage, {
                aspectRatio: 1, // Square aspect ratio
                viewMode: 2,
            });
        };

        reader.readAsDataURL(file); // Read the selected image as data URL
    });

    saveBtn.addEventListener('click', () => {
        // Get the cropped image data from the Cropper
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300
        });

        // Convert the canvas to a Blob and send to the server
        canvas.toBlob((blob) => {
            const formData = new FormData();
            formData.append('image', blob);

            // Send the cropped image to the server via AJAX
            fetch('crop_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                // Redirect back to edit_profile.php
                window.location.href = 'edit_profile.php';
            })
            .catch(error => {
                console.error('Error uploading image:', error);
            });
        });
    });
</script>

</body>
</html>
