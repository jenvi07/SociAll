<?php
include 'header.php';  // Include the header

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $image = '';

    // Handle image upload if any
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        $target = 'uploads/' . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Insert the post into the database
    $user_id = $_SESSION['user_id'];
    $insert_query = "INSERT INTO posts (user_id, content, image, created_at) VALUES ('$user_id', '$content', '$image', NOW())";

    if (mysqli_query($conn, $insert_query)) {
        echo "<p class='success'>Post created successfully!</p>";
    } else {
        echo "<p class='error'>Error creating post: " . mysqli_error($conn) . "</p>";
    }
}
?>

<div class="container">
    <h1>Dashboard</h1>
    <div class="profile-section">
        <h2>Your Profile</h2>
        <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Date of Birth:</strong> <?php echo $user['date_of_birth']; ?></p>
        <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
        <a href="edit_profile.php">Edit Profile</a>
    </div>

    <div class="post-section">
        <h2>Create a Post</h2>
        <form action="dashboard.php" method="POST" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit">Post</button>
        </form>
    </div>

    <div class="posts-list">
        <h2>Your Recent Posts</h2>
        <?php
        $post_query = "SELECT * FROM posts WHERE user_id='$user_id' ORDER BY created_at DESC";
        $post_result = mysqli_query($conn, $post_query);

        if (mysqli_num_rows($post_result) > 0) {
            while ($post = mysqli_fetch_assoc($post_result)): ?>
                <div class="post">
                    <p><?php echo $post['content']; ?></p>
                    <?php if ($post['image']): ?>
                        <img src="uploads/<?php echo $post['image']; ?>" alt="Post Image" width="200">
                    <?php endif; ?>
                    <p><small>Posted on <?php echo $post['created_at']; ?></small></p>
                    <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>">Delete</a>
                </div>
            <?php endwhile; 
        } else {
            echo "<p>No posts to show.</p>";
        }
        ?>
    </div>
</div>

<?php
include 'footer.php';  // Include the footer
?>
