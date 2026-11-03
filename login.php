<?php
session_start();
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // সার্ভার-সাইড ভ্যালিডেশন
    if (empty($username) || empty($password)) {
        $error = 'ইউজারনেম এবং পাসওয়ার্ড দুটিই প্রয়োজন।';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // সেশনে রোল সেভ রাখা
            header("Location: index.php");
            exit;
        } else {
            $error = 'ইউজারনেম বা পাসওয়ার্ড ভুল হয়েছে।';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Task 4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container" style="max-width: 420px;">
    <div class="card shadow p-4">
        <h3 class="text-center mb-3">Sign In</h3>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-3">
                <small>অ্যাকাউন্ট নেই? <a href="register.php">Register</a></small>
            </div>
        </form>
    </div>
</div>
</body>
</html>