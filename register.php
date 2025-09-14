<?php
include 'db_connect.php';
$errors = [];
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (empty($username)) { $errors[] = "Username is required."; }
    if (empty($password)) { $errors[] = "Password is required."; } 
    elseif (strlen($password) < 6) { $errors[] = "Password must be at least 6 characters long."; }
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            $message = "Registration successful! You can now log in.";
        } else {
            if ($stmt->errno == 1062) { $errors[] = "Username already taken."; } 
            else { $errors[] = "Error: " . $stmt->error; }
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to right, #e9ecef, #f8f9fa); }
        .card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { background-color: #0056b3; border-color: #004085; }
    </style>
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card p-4">
            <h2 class="text-center mb-4">Register a New Account</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
        </div>
    </div>
</body>
</html>