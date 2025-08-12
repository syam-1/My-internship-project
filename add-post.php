<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add a New Blog Post</h2>
        <form action="add-post.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>
            
            <input type="submit" value="Submit Post">
        </form>
         <p style="text-align: center;"><a href="index.php">Back to Blog</a></p>
    </div>
</body>
</html>