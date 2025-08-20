<?php
// PHP logic remains the same
session_start();
include 'db_connect.php';
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No user found with that username.";
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card p-4">
            <h2 class="text-center mb-4">Login</h2>

            <?php if(!empty($message)): ?>
                <div class="alert alert-danger"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>
</body>
</html>