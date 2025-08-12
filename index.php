<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';
$sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>My Blog</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span> | 
                <a href="logout.php">Logout</a>
            </div>
        </div>

        <div class="header-actions" style="margin-bottom: 20px;">
          <a href="add-post.php" style="display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; border-radius: 4px;">+ Add New Post</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="post">
                    <h2><?php echo htmlspecialchars($row["title"]); ?></h2>
                    <p class="post-meta">Posted on <?php echo date("F j, Y, g:i a", strtotime($row["created_at"])); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($row["content"])); ?></p>
                    <div class="post-actions">
                        <a href="edit-post.php?id=<?php echo $row['id']; ?>">Edit</a>
                        <a href="delete-post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');" style="color: #dc3545;">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts found. Why not create one?</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>