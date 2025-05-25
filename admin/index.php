<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$admin = getUserById($_SESSION['user_id']);
$allDocs = getAllDocuments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Google Docs Clone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-dashboard">
        <h2>Welcome, Admin <?= htmlspecialchars($admin['name']) ?></h2>
        <p><a href="../logout.php">Logout</a></p>
        <p><a href="manage_users.php">Manage User Accounts</a></p>

        <h3>All Documents in the System</h3>
        <?php if ($allDocs->num_rows > 0): ?>
            <ul>
                <?php while ($doc = $allDocs->fetch_assoc()): ?>
                    <li>
                        <strong><?= htmlspecialchars($doc['title']) ?></strong> 
                        by <?= htmlspecialchars($doc['owner_name']) ?> 
                        (Updated: <?= $doc['updated_at'] ?>)
                        <?php
                        // Only show view link if admin is allowed
                        if ($doc['owner_id'] == $_SESSION['user_id']) {
                            echo "<a href='../users/edit_doc.php?doc_id={$doc['id']}'>Edit</a>";
                        }
                        ?>
                        <a href="../users/activity_logs.php?doc_id=<?= $doc['id'] ?>">View Logs</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No documents found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
