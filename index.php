<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'db.php';

$current_role = $_SESSION['role'] ?? 'editor';

// পেজিনেশন কনফিগারেশন
$limit = 3;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// সার্চ কনফিগারেশন
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_param = "%" . $search . "%";

// Prepared statement দিয়ে মোট পোস্ট গণনা
if (!empty($search)) {
    $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM posts WHERE title LIKE ? OR content LIKE ?");
    $count_stmt->bind_param("ss", $search_param, $search_param);
    $count_stmt->execute();
    $total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $count_res = $conn->query("SELECT COUNT(*) AS total FROM posts");
    $total_rows = $count_res->fetch_assoc()['total'];
}

$total_pages = ceil($total_rows / $limit);

// Prepared statement দিয়ে পোস্ট ফেচ করা
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Task 4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">ApexPlanet Blog</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text text-white me-3">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <span class="badge <?php echo $current_role === 'admin' ? 'bg-danger' : 'bg-info text-dark'; ?> ms-1">
                    <?php echo ucfirst($current_role); ?>
                </span>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row align-items-center mb-4">
        <div class="col-md-6 mb-2 mb-md-0">
            <!-- শুধু Admin ইউজার পোস্ট অ্যাড বাটন দেখতে পাবে -->
            <?php if ($current_role === 'admin'): ?>
                <a href="create.php" class="btn btn-success fw-semibold">+ Add New Post</a>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <form method="GET" action="index.php" class="d-flex">
                <input class="form-control me-2" type="search" name="search" placeholder="Search by title or content..." value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-secondary ms-2">Reset</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- পোস্টের তালিকা -->
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-12 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title text-primary"><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p class="card-text text-secondary"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                            <small class="text-muted">Posted on: <?php echo $row['created_at']; ?></small>
                            <!-- শুধু Admin ইউজার Edit এবং Delete বাটন দেখতে পাবে -->
                            <?php if ($current_role === 'admin'): ?>
                                <div class="mt-3">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm me-1">Edit</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">No posts found matching your criteria.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- পেজিনেশন -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

</body>
</html>