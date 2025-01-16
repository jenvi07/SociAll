<?php
include 'db.php'; // Database connection

session_start();
$user_id = $_SESSION['user_id'];

// Fetch current user data from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form inputs
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $bio = $_POST['bio'];

    // Handle password change only if new password is provided
    if (!empty($_POST['new_password']) && !empty($_POST['current_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($current_password !== $user['password']) {
            echo "<script>alert('Current password is incorrect.');</script>";
        } elseif ($new_password !== $confirm_password) {
            echo "<script>alert('New password and confirm password do not match.');</script>";
        } else {
            // Update password
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $user_id);
            $stmt->execute();
        }
    }

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "img/profile_picture/";
        $file_name = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);

        // Update profile picture path in the database
        $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $target_file, $user_id);
        $stmt->execute();
    }

    // Handle profile picture removal
    if (isset($_POST['remove_profile_pic'])) {
        $default_pic = "img/profile_picture/default_dp.jpeg";
        $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $default_pic, $user_id);
        $stmt->execute();
    }

    // Update other user details
    $sql = "UPDATE users SET name = ?, username = ?, email = ?, date_of_birth = ?, gender = ?, phone = ?, bio = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $username, $email, $date_of_birth, $gender, $phone, $bio, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/css/all.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="css/cropper.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Light background for contrast */
        }

/* Header Style */
header {
    text-align: center;
    color: #dfd8c9;
    background-color: #8c756a;
    opacity: 0.9;
    width: 100%;
    padding: 15px 0;
    margin-bottom: 20px; /* Space below header */
    border-radius: 10px;
}

        .container {
            width: 90%; /* Flexible width for responsiveness */
            max-width: 600px; /* Maximum width */
            margin: auto; /* Center the container */
            padding: 20px;
            background-color: #dfd8c9;
            opacity: 0.95;
            border-radius: 10px;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* Soft shadow */
            display: flex; /* Enable flexbox for the entire container */
            flex-direction: column; /* Arrange items in a column */
            align-items: center; /* Center items horizontally */
        }

        .profile-pic-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .upload-icon, .remove-icon {
            position: absolute;
            background-color: #8c756a;
            color: #dfd8c9;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            border: none;
        }

        .upload-icon {
            bottom: 0;
            right: 0;
        }

        .remove-icon {
            bottom: 0;
            right: 50px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex; /* Enable flexbox */
            flex-direction: column; /* Arrange items in a column */
            align-items: center; /* Center items horizontally */
            text-align: center;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group .btn {
            background-color: #8c756a;
            color: #dfd8c9;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold; /* Make button text bold */
            transition: background-color 0.3s; 
        }
        .form-group .btn:hover {
            background-color: #8c756a; /* Darker shade on hover */
        }

        /* Responsive Styles */
@media (max-width: 768px) {
    .profile-pic {
        width: 120px; /* Smaller profile picture on smaller screens */
        height: 120px;
    }

    .container {
        padding: 15px; /* Less padding on smaller screens */
    }

    .form-group .btn {
        width: 100%; /* Full width button on smaller screens */
    }
}

@media (max-width: 480px) {
    header {
        font-size: 20px; /* Smaller font size for header */
    }

    .form-group label {
        font-size: 14px; /* Smaller font size for labels */
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        font-size: 14px; /* Smaller font size for inputs */
    }

    .form-group .btn {
        font-size: 14px; /* Smaller font size for buttons */
    }
}

    </style>
</head>
<body>
    <header>
        <h2>Edit Profile</h2>
    </header>
    <div class="container">
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <div class="profile-pic-container">
                <img id="profilePreview" src="<?php echo $user['profile_pic']; ?>" alt="Profile Picture" class="profile-pic">
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display:none;">
                <i class="fas fa-camera upload-icon" onclick="document.getElementById('profile_pic').click();"></i>
                <button type="submit" name="remove_profile_pic" class="remove-icon"><i class="fa-solid fa-ban"></i></button>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>">
            </div>

            <div class="form-group">
                <label for="current_password">Current Password (Only if changing)</label>
                <input type="password" id="current_password" name="current_password">
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="5"><?php echo $user['bio']; ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn">Update Profile</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('profile_pic').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
