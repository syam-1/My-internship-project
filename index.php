<?php
// All the PHP logic for session, search, and pagination remains the same
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
include 'db_connect.php';
$posts_per_page = 5;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $posts_per_page;
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = "%" . $search_term . "%";
if (!empty($search_term)) {
    $count_sql = "SELECT COUNT(*) FROM posts WHERE title LIKE ? OR content LIKE ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("ss", $search_param, $search_param);
} else {
    $count_sql = "SELECT COUNT(*) FROM posts";
    $count_stmt = $conn->prepare($count_sql);
}
$count_stmt->execute();
$total_posts = $count_stmt->get_result()->fetch_row()[0];
$total_pages = ceil($total_posts / $posts_per_page);
if (!empty($search_term)) {
    $sql = "SELECT id, title, content, created_at FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $search_param, $search_param, $posts_per_page, $offset);
} else {
    $sql = "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $posts_per_page, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <header class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
            <h1 class="h3 mb-0">My Blog</h1>
            <div>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)!</span> | 
                <a href="logout.php">Logout</a>
            </div>
        </header>

        <div class="row">
            <div class="col-md-8">
                <form action="index.php" method="get" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search for posts..." value="<?php echo htmlspecialchars($search_term); ?>">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="add-post.php" class="btn btn-success">+ Add New Post</a>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row["title"]); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">Posted on <?php echo date("F j, Y, g:i a", strtotime($row["created_at"])); ?></h6>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($row["content"], 0, 200))); ?>...</p>
                        <a href="edit-post.php?id=<?php echo $row['id']; ?>" class="card-link">Edit</a>
                        
                        <?php // Check if the user is an admin to show the Delete link ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            <a href="delete-post.php?id=<?php echo $row['id']; ?>" class="card-link text-danger" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info">No posts found<?php echo !empty($search_term) ? ' for your search "' . htmlspecialchars($search_term) . '"' : '.'; ?></div>
        <?php endif; ?>

        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                    <li class="page-item <?php if($page == $current_page) echo 'active'; ?>">
                        <a class="page-link" href="index.php?page=<?php echo $page; ?>&search=<?php echo urlencode($search_term); ?>">
                            <?php echo $page; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</body>
</html>
<?php $conn->close(); ?>