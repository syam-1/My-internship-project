<?php
// Start the session
session_start();

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
// Include the database connection file
include 'db_connect.php';

// Check if an ID has been passed via GET request
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare a delete statement
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id); // "i" means the parameter is an integer

    if ($stmt->execute()) {
        // Redirect back to the main page after successful deletion
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // If no ID is provided, redirect to the main page
    header("Location: index.php");
    exit();
}
?>