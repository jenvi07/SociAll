<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Social Media Platform</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <div class="navbar">
            <h1 class="logo">SociAll</h1>
            <nav>
                <ul>
                    <li><a href="sociAll.html">Home</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="message.php">Messages</a></li>
                    <li><a href="notification.php">Notifications</a></li>
                    <li><a href="account_settings.php">Settings</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Sidebar with User Profile -->
        <aside class="sidebar">
            <div class="profile">
                <a href="profile.php"></aprofile.php><img src="https://via.placeholder.com/100" alt="Profile Picture"></a>
                <h2>John Doe</h2>
                <p>@johndoe</p>
                <button class="btn-primary">Edit Profile</button>
            </div>
        </aside>

        <!-- Feed Section -->
        <main class="feed">
            <div class="post-form">
                <textarea placeholder="What's on your mind?" rows="3"></textarea>
                <button class="btn-primary">Post</button>
            </div>

            <!-- Sample Post -->
            <div class="post">
                <div class="post-header">
                    <img src="https://via.placeholder.com/50" alt="User Picture">
                    <div class="user-info">
                        <h3>Jane Smith</h3>
                        <p>@janesmith</p>
                    </div>
                    <span class="post-time">2h ago</span>
                </div>
                <div class="post-content">
                    <p>Enjoying a beautiful day at the park! #sunshine</p>
                    <img src="https://via.placeholder.com/400x200" alt="Post Image">
                </div>
                <div class="post-actions">
                    <button><a href="like.php">like</a></button>
                    <button><a href="comments.php">comments</a></button>
                    <button><a href="share.php">share</a></button>
                </div>
            </div>

        </main>

         <!-- Right Sidebar with Search Bar and Suggestions -->
    <aside class="right-sidebar">
    <!-- Search Bar in Right Sidebar -->
    <div class="search-bar">
        <input type="text" placeholder="Search users...">
        <button type="submit">&#128269;</button>
    </div>
            <h2>Who to follow</h2>
            <div class="suggestion">
                <img src="https://via.placeholder.com/50" alt="User Picture">
                <div class="suggestion-info">
                    <h4>Mark Lee</h4>
                    <p>@marklee</p>
                    <button class="btn-secondary">Follow</button>
                </div>
            </div>

            <div class="suggestion">
                <img src="https://via.placeholder.com/50" alt="User Picture">
                <div class="suggestion-info">
                    <h4>Emily Wong</h4>
                    <p>@emilywong</p>
                    <button class="btn-secondary">Follow</button>
                </div>
            </div>
        </aside>
    </div>

    <footer>
        <p>&copy; 2024 SociAll. All rights reserved.</p>
    </footer>
</body>
</html>
