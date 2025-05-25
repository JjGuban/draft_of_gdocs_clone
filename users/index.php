<?php
session_start();
require_once '../core/dbConfig.php';
require_once '../core/models.php';

// Redirect if not logged in or not a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$documents = getUserDocuments($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Google Docs Clone</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="user-dashboard">
        <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
        <p><a href="../logout.php">Logout</a></p>

        <h3>Your Documents</h3>
        <button onclick="location.href='create_doc.php'">+ Create New Document</button>

        <?php if ($documents->num_rows > 0): ?>
            <ul>
                <?php while ($doc = $documents->fetch_assoc()): ?>
                    <li>
                        <strong><?= htmlspecialchars($doc['title']) ?></strong> 
                        (Last updated: <?= $doc['updated_at'] ?>) 
                        <a href="edit_doc.php?doc_id=<?= $doc['id'] ?>">Edit</a> | 
                        <a href="share_doc.php?doc_id=<?= $doc['id'] ?>">Share</a> | 
                        <a href="messages.php?doc_id=<?= $doc['id'] ?>">Messages</a> | 
                        <a href="activity_logs.php?doc_id=<?= $doc['id'] ?>">Logs</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>You have no documents yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
