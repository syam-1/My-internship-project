<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { header('Location: login.php'); exit; }
include 'db_connect.php';
$errors = [];
$post = ['title' => '', 'content' => ''];
$id = $_REQUEST['id'] ?? $_GET['id'] ?? null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $post['title'] = $title;
    $post['content'] = $content;
    if (empty($title)) { $errors[] = "Title is required."; }
    if (empty($content)) { $errors[] = "Content is required."; }
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $content, $id);
        if ($stmt->execute()) { header("Location: index.php"); exit(); } 
        else { $errors[] = "Error updating record."; }
        $stmt->close();
    }
} else {
    $stmt = $conn->prepare("SELECT title, content FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) { $post = $result->fetch_assoc(); } 
    else { exit("Post not found."); }
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
    <style>
        body { background: linear-gradient(to right, #e9ecef, #f8f9fa); }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #004085; }
    </style>
</head>
<body>
    <div class="container mt-5" style="max-width: 800px;">
        <div class="card p-4">
            <h2 class="mb-4">Edit Blog Post</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>
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