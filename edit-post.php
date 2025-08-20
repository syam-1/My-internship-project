<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';
// The PHP logic for updating and fetching the post remains the same
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
    } else {
        exit("Post not found.");
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 800px;">
        <div class="card p-4">
            <h2 class="mb-4">Edit Blog Post</h2>
            <form action="edit-post.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>