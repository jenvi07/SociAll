<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];

    $query = "INSERT INTO users (name, username, email, password, date_of_birth, gender, phone) VALUES ('$name', '$username', '$email', '$password', '$dob', '$gender', '$phone')";
    
    if (mysqli_query($conn, $query)) {
        header('Location: login.php');
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SociAll</title>
    <style>
         /* Global Styles */
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('img/mountains.jpg'); /* Background image */
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: rgba(223, 216, 201, 0.7); /* Theme color with opacity */
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        h2 {
            color: #8c756a; /* Font color */
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],        
        input[type="date"],
        input[type="tel"],
        select{
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);        
        }

        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #8c756a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #735c57;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            color: #8c756a;
            font-size: 14px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .form-footer {
            margin-top: 20px;
        }

        .form-footer a {
            color: #8c756a;
            text-decoration: none;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register for SociAll</h1>
        <div class="form-container">
            <form action="register.php" method="POST">
                <h2>Create Your Account</h2>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <label for="dob">Date of Birth</label>
                <input type="date" name="dob" id="dob" required>
                
                <label for="gender">Gender</label>
                <select name="gender" id="gender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <input type="tel" name="phone" placeholder="Phone Number" required>

                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p>Admin? <a href="admin_login.php">Click here</a></p>
            </form>
        </div>
    </div>
</body>
</html>
