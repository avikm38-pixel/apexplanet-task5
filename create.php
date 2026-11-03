<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Create Post</title></head>
<body>
    <h2>Add New Post</h2>
    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" required style="width:300px;"><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="6" cols="40" required></textarea><br><br>
        <button type="submit">Save Post</button>
    </form>
    <p><a href="index.php">Back to Dashboard</a></p>
</body>
</html>